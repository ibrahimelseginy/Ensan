<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WebAuth
{
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
            if (Auth::check()) {
                Auth::logout();
            }
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors([
                'email' => 'انتهت صلاحية الجلسة أو تم إيقاف الحساب. يرجى تسجيل الدخول مجدداً.'
            ]);
        }

        // 5. Keep session user_id and resolver in sync
        $request->session()->put('user_id', $user->id);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
