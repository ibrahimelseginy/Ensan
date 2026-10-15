<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
