<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class OwnUserOrPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $currentUser = $request->user();
        $routeUser = $request->route('user');
        $routeUserId = is_object($routeUser) ? $routeUser->getKey() : $routeUser;

        if ($currentUser && (int) $currentUser->getKey() === (int) $routeUserId) {
            return $next($request);
        }

        if ($currentUser && Gate::forUser($currentUser)->allows($permission)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
