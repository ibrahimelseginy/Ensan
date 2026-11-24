<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            // Mobile App API Routes
            // NOTE: The 'api' prefix is NOT added automatically in the `then` callback.
            // It is only auto-added for the file passed via the `api:` parameter above.
            // So we must include the full prefix 'api/v1/mobile' here.
            // Final path: /api/v1/mobile/*
            Route::middleware(['api', 'throttle:mobile-api'])
                ->prefix('api/v1/mobile')
                ->group(base_path('routes/mobile_api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'login',
        ]);
        $middleware->alias([
            'auth' => \App\Http\Middleware\WebAuth::class,
            'api_permission' => \App\Http\Middleware\ApiPermission::class,
            'permission' => \App\Http\Middleware\SecurePermissionMiddleware::class,
            'own_user_or_permission' => \App\Http\Middleware\OwnUserOrPermission::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\EnsureHashedRouteKeys::class,
            \App\Http\Middleware\KeepReviewRequestsInContext::class,
            \App\Http\Middleware\AuditLogger::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        $middleware->api(prepend: [
            'throttle:api',
        ], append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'انتهت صلاحية الجلسة، يرجى إعادة المحاولة.'], 419);
            }

            return redirect()->route('login')
                ->with('warning', 'انتهت صلاحية جلسة تسجيل الدخول بسبب عدم النشاط لفترة، يرجى المحاولة الآن.');
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'انتهت صلاحية الجلسة، يرجى إعادة المحاولة.'], 419);
                }

                return redirect()->route('login')
                    ->with('warning', 'انتهت صلاحية جلسة تسجيل الدخول بسبب عدم النشاط لفترة، يرجى المحاولة الآن.');
            }
        });
    })->create();
