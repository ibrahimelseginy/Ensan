<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\EmailLoginMail;
use App\Models\PasswordResetRequest;
use App\Models\SecurityAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class LoginWebController extends Controller
{
    /**
     * Show the login view.
     */
    public function show()
    {
        // 1. If already authenticated via secure session or remember token, redirect to dashboard
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && $user->active && $user->roles->isNotEmpty()) {
                return redirect()->route('dashboard.index');
            }
            // Inactive or role-less session: force clean logout
            Auth::logout();
            request()->session()->invalidate();
        }

        // 2. Clean up legacy insecure plain cookie if present
        if (request()->hasCookie('remember_user')) {
            cookie()->queue(cookie()->forget('remember_user'));
        }

        return view('auth.login');
    }

    /**
     * Handle incoming authentication with full security hardening.
     */
    public function login(Request $request)
    {
        // 1. Strict validation (DoS prevention via max password length)
        $credentials = $request->validate([
            'email'    => 'required|string|email|max:255',
            'password' => 'required|string|min:4|max:128',
            'remember' => 'nullable',
        ], [
            'email.required'    => 'يرجى إدخال البريد الإلكتروني',
            'email.email'       => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.max'         => 'البريد الإلكتروني طويل جداً',
            'password.required' => 'يرجى إدخال كلمة المرور',
            'password.max'      => 'كلمة المرور غير صالحة',
        ]);

        $email = Str::lower(trim($credentials['email']));
        $ip = $request->ip() ?? 'unknown';

        // 2. Rate Limiting / Brute-force & Credential Stuffing Protection
        $throttleKey = Str::transliterate($email . '|' . $ip);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            Log::warning('Security: Login rate limit exceeded', [
                'ip'           => $ip,
                'email'        => $email,
                'available_in' => $seconds,
                'user_agent'   => $request->userAgent(),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => "تم حظر المحاولات مؤقتاً بسبب تكرار المحاولات الخاطئة حرصاً على أمان حسابك. يرجى الانتظار {$seconds} ثانية ثم المحاولة مجدداً."
                ]);
        }

        // 3. User Lookup
        $user = User::with('roles.permissions')->where('email', $email)->first();

        // 4. Timing Attack Mitigation:
        // Always calculate a hash even if user does not exist to guarantee uniform response time
        $dummyHash = '$2y$10$eA3Z1p8WdFkGmQbC7x1YLeZqQhW8mNoIjKlMnOpQrStUvWxYz0123';
        $passwordValid = $user ? Hash::check($credentials['password'], $user->password) : Hash::check($credentials['password'], $dummyHash);

        if (!$user || !$passwordValid) {
            // Count failed attempt, decay window 60s
            RateLimiter::hit($throttleKey, 60);

            Log::notice('Security: Failed login attempt', [
                'email'      => $email,
                'ip'         => $ip,
                'user_agent' => $request->userAgent(),
                'user_found' => (bool)$user,
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'بيانات الدخول غير صحيحة']);
        }

        // 5. Account Active Status Check
        if (!$user->active) {
            RateLimiter::hit($throttleKey, 60);
            Log::warning('Security: Inactive user login blocked', ['user_id' => $user->id, 'email' => $email]);
            return back()->withInput($request->only('email'))->withErrors(['email' => 'هذا الحساب غير نشط، يرجى مراجعة مسؤول النظام']);
        }

        // 5.1 Volunteer Check: Volunteers are data profiles only, not login users
        if ($user->is_volunteer) {
            RateLimiter::hit($throttleKey, 60);
            Log::warning('Security: Volunteer login blocked (data profile only)', ['user_id' => $user->id, 'email' => $email]);
            return back()->withInput($request->only('email'))->withErrors(['email' => 'حسابات المتطوعين مخصصة لحفظ البيانات والمتابعة فقط وليست لتسجيل الدخول إلى النظام.']);
        }

        // 6. Role & Permission Verification
        if ($user->roles->isEmpty()) {
            RateLimiter::hit($throttleKey, 60);
            Log::warning('Security: Role-less user login blocked', ['user_id' => $user->id, 'email' => $email]);
            return back()->withInput($request->only('email'))->withErrors(['email' => 'هذا الحساب غير مرتبط بدور أو صلاحيات للدخول إلى المنظومة']);
        }

        // 7. Successful Authentication: Clear Rate Limiting
        RateLimiter::clear($throttleKey);

        // 8. Cryptographic Login with Laravel's built-in signed remember token
        $remember = $request->boolean('remember', true);

        return $this->completeLogin($request, $user, $remember, $ip, 'password');
    }

    /**
     * Send a 6-digit OTP & Magic Login Link to the user's email for passwordless login.
     */
    public function sendEmailLoginCode(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255',
        ], [
            'email.required' => 'يرجى إدخال البريد الإلكتروني',
            'email.email'    => 'صيغة البريد الإلكتروني غير صحيحة',
        ]);

        $email = Str::lower(trim($validated['email']));
        $ip = $request->ip() ?? 'unknown';
        $throttleKey = 'email_login_send|' . Str::transliterate($email . '|' . $ip);

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->only('email'))
                ->with('login_tab', 'email_otp')
                ->withErrors([
                    'email_otp' => "تم تجاوز الحد المسموح لطلب الأكواد. يرجى الانتظار {$seconds} ثانية والمحاولة مجدداً.",
                ]);
        }

        RateLimiter::hit($throttleKey, 300);

        $user = User::with('roles.permissions')->where('email', $email)->first();

        if ($user && $user->active && !$user->is_volunteer && $user->roles->isNotEmpty()) {
            PasswordResetRequest::where('user_id', $user->id)
                ->where('channel', 'email_otp')
                ->where('is_used', false)
                ->update(['is_used' => true]);

            $selector = Str::random(16);
            $validator = Str::random(32);
            $otpCode = sprintf('%06d', random_int(100000, 999999));

            PasswordResetRequest::create([
                'user_id'              => $user->id,
                'token_selector'       => $selector,
                'token_validator_hash' => hash('sha256', $validator),
                'otp_code_hash'        => Hash::make($otpCode),
                'channel'              => 'email_otp',
                'target_destination'   => $user->email,
                'attempts_count'       => 0,
                'max_attempts'         => 3,
                'is_used'              => false,
                'expires_at'           => now()->addMinutes(10),
                'ip_address'           => $ip,
                'user_agent'           => substr((string) $request->userAgent(), 0, 500),
            ]);

            $magicUrl = route('login.email.magic', [
                'token' => $selector . '.' . $validator,
            ]);

            try {
                Mail::to($user->email)->send(new EmailLoginMail($user, $otpCode, $magicUrl, 10));

                SecurityAuditLog::record(
                    $user->id,
                    'email_login_code_sent',
                    'info',
                    ['email' => $user->email, 'selector' => $selector],
                    'email'
                );
            } catch (\Throwable $e) {
                Log::error('Security: Failed to send email login OTP', [
                    'user_id' => $user->id,
                    'email'   => $user->email,
                    'error'   => $e->getMessage(),
                ]);

                return back()
                    ->withInput($request->only('email'))
                    ->with('login_tab', 'email_otp')
                    ->withErrors([
                        'email_otp' => 'تعذر إرسال البريد الإلكتروني حالياً بسبب إعدادات خادم البريد على السيرفر. يرجى التواصل مع مسؤول النظام.',
                    ]);
            }
        }

        // Prevent user enumeration: always show neutral confirmation
        return back()
            ->with('login_tab', 'email_otp')
            ->with('email_login_sent', $email)
            ->with('email_login_status', 'إذا كان البريد الإلكتروني مسجلاً ونشطاً لدينا، فقد تم إرسال كود التحقق ورابط الدخول المباشر إليه.');
    }

    /**
     * Verify 6-digit OTP code sent via email and log the user in.
     */
    public function verifyEmailLoginCode(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|string|email|max:255',
            'otp_code' => 'required|string|min:6|max:6',
            'remember' => 'nullable',
        ], [
            'email.required'    => 'يرجى إدخال البريد الإلكتروني',
            'otp_code.required' => 'يرجى إدخال كود التحقق المكون من 6 أرقام',
            'otp_code.min'      => 'كود التحقق يجب أن يتكون من 6 أرقام',
            'otp_code.max'      => 'كود التحقق يجب أن يتكون من 6 أرقام',
        ]);

        $email = Str::lower(trim($validated['email']));
        $cleanOtp = preg_replace('/\D/', '', (string) $validated['otp_code']);
        $ip = $request->ip() ?? 'unknown';
        $throttleKey = 'email_login_verify|' . Str::transliterate($email . '|' . $ip);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->with('login_tab', 'email_otp')
                ->with('email_login_sent', $email)
                ->withErrors(['otp_code' => "تم حظر المحاولات مؤقتاً. يرجى الانتظار {$seconds} ثانية."]);
        }

        $user = User::with('roles.permissions')->where('email', $email)->first();

        if (!$user || !$user->active || $user->is_volunteer || $user->roles->isEmpty()) {
            RateLimiter::hit($throttleKey, 60);
            return back()
                ->with('login_tab', 'email_otp')
                ->with('email_login_sent', $email)
                ->withErrors(['otp_code' => 'بيانات الحساب غير صالحة أو الحساب غير مفعل.']);
        }

        $req = PasswordResetRequest::where('user_id', $user->id)
            ->where('channel', 'email_otp')
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->orderByDesc('id')
            ->first();

        if (!$req || $req->attempts_count >= $req->max_attempts || strlen($cleanOtp) !== 6 || !Hash::check($cleanOtp, (string) $req->otp_code_hash)) {
            RateLimiter::hit($throttleKey, 60);

            if ($req) {
                $req->increment('attempts_count');
                if ($req->attempts_count >= $req->max_attempts) {
                    $req->update(['is_used' => true]);
                }
            }

            return back()
                ->with('login_tab', 'email_otp')
                ->with('email_login_sent', $email)
                ->withErrors(['otp_code' => 'كود التحقق غير صحيح أو منتهي الصلاحية.']);
        }

        $req->update([
            'is_used' => true,
            'used_at' => now(),
        ]);

        RateLimiter::clear($throttleKey);
        $remember = $request->boolean('remember', true);

        return $this->completeLogin($request, $user, $remember, $ip, 'email_otp');
    }

    /**
     * One-click Magic Login via signed email link.
     */
    public function magicLogin(Request $request, string $token)
    {
        if (!str_contains($token, '.')) {
            return redirect()->route('login')
                ->with('login_tab', 'email_otp')
                ->withErrors(['email_otp' => 'رابط تسجيل الدخول غير صالح.']);
        }

        [$selector, $validator] = explode('.', $token, 2);

        $req = PasswordResetRequest::where('token_selector', $selector)
            ->where('channel', 'email_otp')
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$req || !hash_equals((string) $req->token_validator_hash, hash('sha256', $validator))) {
            return redirect()->route('login')
                ->with('login_tab', 'email_otp')
                ->withErrors(['email_otp' => 'رابط تسجيل الدخول غير صالح أو انتهت صلاحيته. يرجى طلب رابط جديد.']);
        }

        $user = User::with('roles.permissions')->find($req->user_id);

        if (!$user || !$user->active || $user->is_volunteer || $user->roles->isEmpty()) {
            return redirect()->route('login')
                ->with('login_tab', 'email_otp')
                ->withErrors(['email_otp' => 'هذا الحساب غير نشط أو غير مصرح له بالدخول.']);
        }

        $req->update([
            'is_used' => true,
            'used_at' => now(),
        ]);

        $ip = $request->ip() ?? 'unknown';
        return $this->completeLogin($request, $user, true, $ip, 'email_magic_link');
    }

    /**
     * Complete authenticated session setup with security fingerprints and open-redirect protection.
     */
    private function completeLogin(Request $request, User $user, bool $remember, string $ip, string $method = 'password')
    {
        Auth::login($user, $remember);

        // Session Fixation Prevention: Regenerate session ID
        $request->session()->regenerate();
        $request->session()->put('user_id', $user->id);
        $request->session()->put('_sec_ua_fp', hash('sha256', ($request->userAgent() ?? '') . '|' . config('app.key')));
        $request->session()->put('_sec_pwd_hash', (string) $user->password);
        $request->session()->put('_sec_last_activity', time());
        $request->session()->put('_sec_login_ip', $ip);

        // Clean legacy cookie if present
        cookie()->queue(cookie()->forget('remember_user'));

        if ($user->hasRole('admin')) {
            $request->session()->flash('show_admin_notifications', true);
        }

        Log::info('Security: Successful login', [
            'user_id'  => $user->id,
            'email'    => $user->email,
            'ip'       => $ip,
            'remember' => $remember,
            'method'   => $method,
        ]);

        // 9. Open Redirect Prevention: Ensure target is internal
        $intendedUrl = $request->session()->pull('url.intended', route('dashboard.index'));
        $parsedUrl = parse_url($intendedUrl);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        if (isset($parsedUrl['host']) && $parsedUrl['host'] !== $request->getHost() && $parsedUrl['host'] !== $appHost) {
            $intendedUrl = route('dashboard.index');
        }

        return redirect()->to($intendedUrl);
    }

    /**
     * Handle logout with complete token invalidation & session cleanup.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user instanceof User) {
            // Rotate remember token in database to instantly invalidate any existing remember cookies
            $user->setRememberToken(Str::random(60));
            $user->save();
        }

        Auth::logout();

        // Invalidate session and regenerate CSRF token to prevent session reuse
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Expire legacy cookie if present
        cookie()->queue(cookie()->forget('remember_user'));

        return redirect()->route('login');
    }
}
