<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\PasswordSecurityService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforcePasswordSecurityPolicy
{
    public function __construct(
        protected PasswordSecurityService $securityService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip guests or unauthenticated users
        if (!$user) {
            return $next($request);
        }

        // Exclude password change, logout, and profile update routes
        $route = $request->route();
        $routeName = $route ? $route->getName() : '';

        $exemptRoutes = [
            'logout',
            'password.force-change',
            'password.force-update',
            'password.snooze',
            'password.request',
            'password.email',
            'password.reset',
            'password.update',
        ];

        if (in_array($routeName, $exemptRoutes, true)) {
            return $next($request);
        }

        // Evaluate Security Phase
        $status = $this->securityService->evaluateSecurityStatus($user);

        switch ($status) {
            case 'locked':
                Auth::logout();
                $request->session()->invalidate();
                return redirect()->route('login')->withErrors([
                    'email' => 'تم تجميد هذا الحساب مؤقتاً لأسباب أمنية. يرجى التواصل مع إدارة النظام.'
                ]);

            case 'hard_lock':
                return redirect()->route('password.force-change')->with('warning', 'انتهت فترة السماح لتحديث كلمة المرور. يرجى تعيين كلمة مرور جديدة قوية لمتابعة استخدام النظام.');

            case 'soft_lock':
                $request->session()->flash('security_soft_lock', true);

                // Block any mutating operations (Read-Only enforcement)
                if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'error' => 'تم تجميد صلاحيات الإجراءات (وضع القراءة فقط) نظراً لتجاوز موعد تحديث كلمة المرور. يرجى تحديث كلمة المرور لاستعادة الصلاحيات.',
                            'requires_password_change' => true,
                        ], 403);
                    }

                    return back()->with('error', 'تم تجميد صلاحيات الإجراءات (وضع القراءة فقط) نظراً لتجاوز موعد تحديث كلمة المرور. يرجى تحديث كلمة المرور لاستعادة الصلاحيات فورا.');
                }
                break;

            case 'friction':
                $request->session()->flash('show_friction_modal', true);
                break;

            case 'grace':
                $request->session()->flash('show_grace_alert', true);
                break;

            case 'compliant':
            default:
                // Normal compliant state
                break;
        }

        return $next($request);
    }
}
