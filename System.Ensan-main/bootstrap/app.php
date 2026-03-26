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
            Route::middleware('api')
                ->prefix('api/v1/mobile')
                ->group(base_path('routes/mobile_api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\AuditLogger::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\CorsMiddleware::class,
        ]);
        $middleware->append(
            \App\Http\Middleware\CorsMiddleware::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
