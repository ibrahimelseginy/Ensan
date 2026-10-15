<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WebAuth
{
    /**
     * الحد الأقصى لخمول الجلسة بالثواني (120 دقيقة = 7200 ثانية).
     * يتم إبطال الجلسة تلقائياً في حال عدم وجود أي نشاط لحماية الحساب عند ترك الجهاز مفتوحاً.
     */
    protected const INACTIVITY_TIMEOUT = 7200;

    public function handle(Request $request, Closure $next)
    {
        // 1. Resolve authenticated user via Laravel's official guard (session + cryptographic remember token)
        $user = Auth::user();

        // 2. If Auth::user() is null, check session user_id
        if (!$user && $request->session()->has('user_id')) {
            $sessionUserId = (int) $request->session()->get('user_id');
            $user = User::with('roles.permissions')->find($sessionUserId);
            if ($user) {
                Auth::login($user, false);
            }
        }

        // 3. Clean up legacy insecure plaintext cookie if present
        if ($request->hasCookie('remember_user')) {
            cookie()->queue(cookie()->forget('remember_user'));
        }

        // 4. Verify user exists, is active, and has assigned roles/permissions
        if (!$user || !$user->active || $user->roles->isEmpty()) {
            return $this->terminateSession($request, 'انتهت صلاحية الجلسة أو تم إيقاف الحساب. يرجى تسجيل الدخول مجدداً.');
        }

        // 5. Anti-Session Hijacking (بصمة المتصفح لمنع سرقة واستنساخ الجلسة)
        // التحقق من أن الطلب وارد من نفس بيئة المتصفح الأصلية التي تم تسجيل الدخول منها
        $currentFp = hash('sha256', ($request->userAgent() ?? '') . '|' . config('app.key'));
        if ($request->session()->has('_sec_ua_fp')) {
            if (!hash_equals((string) $request->session()->get('_sec_ua_fp'), $currentFp)) {
                Log::warning('Security: Session hijacking attempt detected (User-Agent mismatch)', [
                    'user_id' => $user->id,
                    'ip' => $request->ip(),
                    'expected' => $request->session()->get('_sec_ua_fp'),
                    'actual_ua' => $request->userAgent(),
                ]);
                return $this->terminateSession($request, 'تم اكتشاف محاولة استخدام غير مصرح بها للجلسة من متصفح مختلف. لحمايتك تم إنهاء الجلسة فوراً.');
            }
        } else {
            $request->session()->put('_sec_ua_fp', $currentFp);
        }

        // 6. Password Change Binding (إبطال الجلسات الأخرى فور تغيير كلمة المرور)
        if ($request->session()->has('_sec_pwd_hash')) {
            if (!hash_equals((string) $request->session()->get('_sec_pwd_hash'), (string) $user->password)) {
                Log::notice('Security: Session invalidated due to password change', ['user_id' => $user->id]);
                return $this->terminateSession($request, 'تم تغيير كلمة المرور الخاصة بحسابك، يرجى تسجيل الدخول بكلمة المرور الجديدة.');
            }
        } else {
            $request->session()->put('_sec_pwd_hash', (string) $user->password);
        }

        // 7. Sliding Inactivity Timeout (إنهاء الجلسة تلقائياً عند خمول المستخدم)
        $now = time();
        $lastActivity = $request->session()->get('_sec_last_activity');
        if ($lastActivity && ($now - (int) $lastActivity > self::INACTIVITY_TIMEOUT)) {
            Log::info('Security: Session expired due to inactivity', ['user_id' => $user->id]);
            return $this->terminateSession($request, 'انتهت صلاحية الجلسة بسبب عدم النشاط لفترة طويلة حرصاً على أمان بياناتك. يرجى إعادة تسجيل الدخول.');
        }
        $request->session()->put('_sec_last_activity', $now);

        // 8. Keep session user_id and resolver in sync
        $request->session()->put('user_id', $user->id);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }

    /**
     * إنهاء الجلسة بطريقة آمنة وتجديد التوكن وإفراغ الذاكرة
     */
    protected function terminateSession(Request $request, string $errorMessage)
    {
        if (Auth::check()) {
            Auth::logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => $errorMessage
        ]);
    }
}
