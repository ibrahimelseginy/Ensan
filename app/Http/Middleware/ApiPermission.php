<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class ApiPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = $request->user();
        abort_unless($user instanceof \App\Models\User, 401);
        abort_unless($user->hasPermission($permission), 403);
        return $next($request);
    }
}
