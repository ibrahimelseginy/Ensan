<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\PasswordResetMail;
use App\Models\PasswordHistory;
use App\Models\PasswordResetRequest;
use App\Models\SecurityAuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordSecurityService
{
    /**
     * Generate an authenticated password reset request and dispatch unified email (Link + OTP).
     */
    public function sendResetEmail(User $user, ?string $ip = null, ?string $userAgent = null): PasswordResetRequest
    {
        // 1. Invalidate any existing pending requests for this user
        PasswordResetRequest::where('user_id', $user->id)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // 2. Generate Split Token (Selector + Validator) and 6-Digit OTP
        $selector = Str::random(16);
        $validator = Str::random(32);
        $validatorHash = hash('sha256', $validator);

        $otpCode = sprintf('%06d', random_int(100000, 999999));
        $otpHash = Hash::make($otpCode);

        // 3. Save to database
        $requestRecord = PasswordResetRequest::create([
            'user_id'              => $user->id,
            'token_selector'       => $selector,
            'token_validator_hash' => $validatorHash,
            'otp_code_hash'        => $otpHash,
            'channel'              => 'email_link',
            'target_destination'   => $user->email,
            'attempts_count'       => 0,
            'max_attempts'         => 3,
            'is_used'              => false,
            'expires_at'           => now()->addMinutes(15),
            'ip_address'           => $ip ?? request()->ip(),
            'user_agent'           => substr((string) ($userAgent ?? request()->userAgent()), 0, 500),
        ]);

        // 4. Construct Magic Reset URL
        $resetUrl = route('password.reset', [
            'token'    => $selector . '.' . $validator,
            'email'    => $user->email,
        ]);

        // 5. Send Unified Email via Gmail / Google Workspace SMTP
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $otpCode, $resetUrl));

            SecurityAuditLog::record(
                $user->id,
                'password_reset_requested',
                'info',
                ['email' => $user->email, 'selector' => $selector],
                'email'
            );
        } catch (\Throwable $e) {
            Log::error('Security: Failed to send password reset email', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);

            SecurityAuditLog::record(
                $user->id,
                'password_reset_email_failed',
                'danger',
                ['error' => $e->getMessage()],
                'email'
            );
        }

        return $requestRecord;
    }

    /**
     * Validate reset request via Magic Link token or 6-digit OTP code.
     */
    public function validateResetRequest(string $email, ?string $token = null, ?string $otp = null): ?PasswordResetRequest
    {
        $user = User::where('email', Str::lower(trim($email)))->first();
        if (!$user) {
            return null;
        }

        // Mode 1: Validation via Token (Selector.Validator)
        if (!empty($token) && str_contains($token, '.')) {
            [$selector, $validator] = explode('.', $token, 2);

            $req = PasswordResetRequest::where('user_id', $user->id)
                ->where('token_selector', $selector)
                ->where('is_used', false)
                ->where('expires_at', '>', now())
                ->first();

            if ($req) {
                $expectedHash = hash('sha256', $validator);
                if (hash_equals($req->token_validator_hash, $expectedHash)) {
                    return $req;
                }
            }
        }

        // Mode 2: Validation via 6-Digit OTP Code
        if (!empty($otp)) {
            $cleanOtp = preg_replace('/\D/', '', $otp);

            $req = PasswordResetRequest::where('user_id', $user->id)
                ->where('is_used', false)
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            if ($req) {
                if ($req->attempts_count >= $req->max_attempts) {
                    $req->update(['is_used' => true]);
                    SecurityAuditLog::record($user->id, 'otp_max_attempts_exceeded', 'danger', [], 'email');
                    return null;
                }

                if ($req->otp_code_hash && Hash::check($cleanOtp, $req->otp_code_hash)) {
                    return $req;
                }

                // Increment failed attempt
                $req->increment('attempts_count');
                SecurityAuditLog::record($user->id, 'otp_attempt_failed', 'warning', [
                    'attempts' => $req->attempts_count
                ], 'email');
            }
        }

        return null;
    }

    /**
     * Execute password update and enforce anti-recycling, audit log, and session revocation.
     */
    public function updatePassword(User $user, string $newPassword, ?PasswordResetRequest $resetRequest = null): void
    {
        $oldHash = $user->password;
        $newHash = Hash::make($newPassword);
        $policy = $user->policy;

        // 1. Save old password to history
        if (!empty($oldHash)) {
            PasswordHistory::create([
                'user_id'       => $user->id,
                'password_hash' => $oldHash,
                'created_at'    => now(),
            ]);

            // Prune excess histories
            $keepLimit = max(1, $policy->history_limit);
            $historyIds = PasswordHistory::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->take($keepLimit)
                ->pluck('id');

            PasswordHistory::where('user_id', $user->id)
                ->whereNotIn('id', $historyIds)
                ->delete();
        }

        // 2. Update User Password State
        $user->forceFill([
            'password'                  => $newHash,
            'password_changed_at'       => now(),
            'password_expires_at'       => now()->addDays($policy->max_age_days),
            'force_password_change'     => false,
            'password_snooze_count'     => 0,
            'password_snoozed_until'    => null,
            'password_version'          => (int) ($user->password_version ?? 1) + 1,
            'consecutive_failed_logins' => 0,
            'locked_until'              => null,
            'remember_token'            => Str::random(60), // Rotate remember token to invalidate other devices
        ])->save();

        // 3. Mark request as consumed if present
        if ($resetRequest) {
            $resetRequest->update([
                'is_used' => true,
                'used_at' => now(),
            ]);
        }

        // 4. Record Audit Log
        SecurityAuditLog::record(
            $user->id,
            'password_changed_successfully',
            'info',
            ['method' => $resetRequest ? 'reset_flow' : 'direct_change'],
            'web'
        );
    }

    /**
     * Evaluate the Progressive Enforcement State for an authenticated user.
     * Returns: 'compliant', 'grace', 'friction', 'soft_lock', 'hard_lock'
     */
    public function evaluateSecurityStatus(User $user): string
    {
        // 1. Account Locked due to security incident
        if ($user->locked_until && $user->locked_until->isFuture()) {
            return 'locked';
        }

        // 2. Explicit force change
        if ($user->force_password_change) {
            return 'hard_lock';
        }

        $policy = $user->policy;
        $changedAt = $user->password_changed_at ?? $user->created_at ?? now();
        $expiresAt = $user->password_expires_at ?? $changedAt->copy()->addDays($policy->max_age_days);

        // If not expired yet, user is compliant
        if (now()->lessThan($expiresAt)) {
            // Check if within 7 days of approaching expiration
            $daysUntilExpiry = now()->diffInDays($expiresAt, false);
            if ($daysUntilExpiry <= 7) {
                return 'grace';
            }
            return 'compliant';
        }

        // User is EXPIRED: Calculate days overdue
        $daysOverdue = (int) $expiresAt->diffInDays(now());

        // Phase 1: Grace Period (Days 1 to 7 overdue)
        if ($daysOverdue <= $policy->grace_period_days) {
            return 'grace';
        }

        // Phase 2: Friction Gate (Days 8 to 14 overdue)
        if ($daysOverdue <= 14) {
            // Check if currently snoozed
            if ($user->password_snoozed_until && $user->password_snoozed_until->isFuture()) {
                return 'grace';
            }
            return 'friction';
        }

        // Phase 3: Soft Lockdown / Read-Only (Days 15 to 20 overdue)
        if ($daysOverdue <= 20) {
            return 'soft_lock';
        }

        // Phase 4: Hard Lockout Gate (Day 21+ overdue)
        return 'hard_lock';
    }
}
