<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Traits\HashedRouteKey;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureHashedRouteKeys
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isMethodSafe() || !$request->route()?->getName()) {
            return $next($request);
        }

        $route = $request->route();
        $parameters = $route->parameters();
        $shouldRedirect = false;

        foreach ($parameters as $name => $parameter) {
            if (!$parameter instanceof Model || !in_array(HashedRouteKey::class, class_uses_recursive($parameter), true)) {
                continue;
            }

            $original = (string) $route->originalParameter($name, '');

            if (ctype_digit($original)) {
                $shouldRedirect = true;
            }
        }

        if (!$shouldRedirect) {
            return $next($request);
        }

        $url = route($route->getName(), $parameters);
        $query = $request->getQueryString();

        return redirect()->to($query ? $url . '?' . $query : $url, 301);
    }
}
