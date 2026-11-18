<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SecurityAuditLog;
use App\Models\User;
use App\Rules\StrictPasswordRule;
use App\Services\PasswordSecurityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class PasswordSecurityController extends Controller
{
    public function __construct(
        protected PasswordSecurityService $securityService
    ) {}

    /**
     * Show password recovery request form (Email input).
     */
    public function showForgotForm(): View
    {
        return view('auth.passwords.email');
    }

    /**
     * Handle sending recovery email via Gmail (Link + OTP).
     */
    public function sendResetCode(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ], [
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email'    => 'صيغة البريد الإلكتروني غير صحيحة.',
        ]);

        $email = Str::lower(trim($request->input('email')));
        $throttleKey = 'pwd_reset|' . $email . '|' . ($request->ip() ?? '127.0.0.1');

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "تم تجاوز الحد المسموح من الطلبات. يرجى الانتظار {$seconds} ثانية والمحاولة مجدداً.");
        }

        RateLimiter::hit($throttleKey, 300); // 5 minutes decay

        $user = User::where('email', $email)->first();

        if ($user && $user->active) {
            $this->securityService->sendResetEmail($user, $request->ip(), $request->userAgent());
        }

        // Always show success message to prevent user enumeration
        return redirect()->route('password.reset', ['email' => $email])
            ->with('status', 'إذا كان هذا البريد مسجلاً لدينا، فقد تم إرسال كود التحقق ورابط التعيين إليه فوراً عبر الجيميل (Gmail).');
    }

    /**
     * Show reset password form (accepts token or OTP).
     */
    public function showResetForm(Request $request, ?string $token = null): View
    {
        $email = $request->query('email', '');
        return view('auth.passwords.reset', [
            'token' => $token ?? $request->query('token'),
            'email' => $email,
        ]);
    }

    /**
     * Process password reset via Magic Link or 6-Digit OTP.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $email = Str::lower(trim($request->input('email', '')));
        $user = User::where('email', $email)->first();

        $request->validate([
            'email'    => 'required|email',
            'password' => ['required', 'string', 'confirmed', new StrictPasswordRule($user)],
        ], [
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        if (!$user || !$user->active) {
            return back()->withInput()->with('error', 'الحساب غير موجود أو غير نشط.');
        }

        $token = $request->input('token');
        $otp = $request->input('otp_code');

        if (empty($token) && empty($otp)) {
            return back()->withInput()->with('error', 'يرجى إدخال كود التحقق الرقمي (OTP) أو استخدام رابط البريد.');
        }

        $resetRequest = $this->securityService->validateResetRequest($email, $token, $otp);

        if (!$resetRequest) {
            return back()->withInput()->with('error', 'كود التحقق أو الرابط غير صحيح، أو انتهت صلاحيته. يرجى طلب كود جديد.');
        }

        // Complete password reset
        $this->securityService->updatePassword($user, $request->input('password'), $resetRequest);

        // Auto login user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard.index')
            ->with('success', 'تم تعيين كلمة المرور الجديدة بنجاح وتسجيل الدخول إلى حسابك بأمان.');
    }

    /**
     * Show Mandatory Force Password Change Form (Hard Lockout Phase).
     */
    public function showForceChangeForm(): View
    {
        $user = Auth::user();
        return view('auth.passwords.force-change', compact('user'));
    }

    /**
     * Handle Mandatory Force Password Change submission.
     */
    public function updateForcePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate([
            'password' => ['required', 'string', 'confirmed', new StrictPasswordRule($user)],
        ], [
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $this->securityService->updatePassword($user, $request->input('password'));

        return redirect()->route('dashboard.index')
            ->with('success', 'تم تحديث كلمة المرور بنجاح وتفعيل كامل صلاحيات حسابك في المنظومة.');
    }

    /**
     * Handle Snooze Request during Phase 2 (Active Friction Gate).
     */
    public function snooze(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $policy = $user->policy;
        $maxSnoozes = $policy->max_snoozes;
        $currentSnoozes = (int) $user->password_snooze_count;

        if ($currentSnoozes >= $maxSnoozes) {
            return back()->with('error', 'لقد استنفدت كامل رصيد مرات التأجيل المسموحة لك (' . $maxSnoozes . ' مرات). يرجى تغيير كلمة المرور فوراً.');
        }

        $snoozeHours = $policy->snooze_hours;
        $user->forceFill([
            'password_snooze_count'  => $currentSnoozes + 1,
            'password_snoozed_until' => now()->addHours($snoozeHours),
        ])->save();

        SecurityAuditLog::record(
            $user->id,
            'password_snooze_used',
            'warning',
            [
                'snooze_number'   => $currentSnoozes + 1,
                'remaining'       => $maxSnoozes - ($currentSnoozes + 1),
                'snoozed_until'   => $user->password_snoozed_until?->toIso8601String(),
            ],
            'web'
        );

        $remaining = $maxSnoozes - ($currentSnoozes + 1);
        return back()->with('info', "تم تأجيل تنبيه كلمة المرور لـ {$snoozeHours} ساعات بنجاح (المتبقي: {$remaining} تأجيلات).");
    }
}
