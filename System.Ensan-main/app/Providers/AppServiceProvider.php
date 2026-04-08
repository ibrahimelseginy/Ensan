<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        // Register Global Permission Gate
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            // Check if user has the specific permission key
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

