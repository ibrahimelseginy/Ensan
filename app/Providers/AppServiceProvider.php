<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Features\WebsiteDonations\Interfaces\WebsiteDonationProcessorInterface::class,
            \App\Features\WebsiteDonations\Services\WebsiteDonationService::class
        );

        $this->app->bind(
            \App\Features\WebsiteContent\Interfaces\DonationPageSettingsInterface::class,
            \App\Features\WebsiteContent\Services\DonationPageSettingsService::class
        );
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        // ─── Custom: قاعدة رفع الصور تقبل جميع أنواع الصور الشائعة ───
        Validator::extend('any_image', function ($attribute, $value, $parameters, $validator) {
            if (!($value instanceof \Illuminate\Http\UploadedFile)) {
                return false;
            }
            $allowedMimes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
                'image/webp', 'image/bmp', 'image/svg+xml', 'image/tiff',
                'image/heic', 'image/heif', 'image/avif', 'image/x-icon',
                'image/vnd.microsoft.icon',
            ];
            return in_array($value->getMimeType(), $allowedMimes, true);
        }, 'يجب أن يكون الملف صورة صالحة (JPEG, PNG, GIF, WebP, BMP, SVG, TIFF, HEIC, AVIF).');


        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            // First, allow admins to bypass everything
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return true;
            }

            // Otherwise, check if user has the specific permission key
            if (method_exists($user, 'hasPermission') && $user->hasPermission($ability)) {
                return true;
            }
            return null; // Fall through to other checks
        });

        if (\DB::connection()->getDriverName() === 'sqlite') {
            $pdo = \DB::connection()->getPdo();
            $pdo->sqliteCreateFunction('REGEXP', function ($pattern, $value) {
                if (is_null($value)) return false;
                return (bool) preg_match('/' . str_replace('/', '\/', $pattern) . '/i', $value);
            });
        }
    }
}

