<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;

class SecurePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        foreach ($permissions as $permGroup) {
            foreach (explode('|', $permGroup) as $perm) {
                $perm = trim($perm);
                if ($perm !== '' && Gate::allows($perm)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة.');
    }
}
