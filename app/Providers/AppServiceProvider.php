<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Laravel merges framework defaults; retain only the supported SQL engine.
        config(['database.connections' => ['mysql' => config('database.connections.mysql')]]);
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
                'image/webp', 'image/bmp', 'image/tiff',
                'image/heic', 'image/heif', 'image/avif', 'image/x-icon',
                'image/vnd.microsoft.icon',
            ];
            return in_array($value->getMimeType(), $allowedMimes, true);
        }, 'يجب أن يكون الملف صورة صالحة (JPEG, PNG, GIF, WebP, BMP, TIFF, HEIC, AVIF).');


        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            // First, allow admins and general managers to bypass everything (Arabic and English)
            if (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'super_admin', 'manager', 'مدير المؤسسة بالكامل', 'إدارة'])) {
                return true;
            }

            // Otherwise, check if user has the specific permission key
            if (method_exists($user, 'hasPermission') && $user->hasPermission($ability)) {
                return true;
            }
            return null; // Fall through to other checks
        });

        // منظومة البحث المبدئي واستقبال الحالات: إتاحة الوصول لمدراء المشاريع والباحثين الميدانيين ومسؤولي المستفيدين
        \Illuminate\Support\Facades\Gate::define('mobile.case_applications.view', function ($user) {
            if ($user->hasRole(['admin', 'super_admin', 'manager', 'مدير المؤسسة بالكامل', 'إدارة', 'مدراء المشاريع', 'باحث ميداني', 'الاستقبال', 'مدير مشروع زاد', 'مدير مشروع مدرار', 'مدير مشروع بعثاء الأمل', 'مدير مشروع كسوة'])) {
                return true;
            }
            return $user->hasPermission('mobile.case_applications.view')
                || $user->hasPermission('mobile.view')
                || $user->hasPermission('beneficiaries.view')
                || $user->hasPermission('beneficiaries.create')
                || $user->hasPermission('visits.view')
                || $user->hasPermission('visits.create');
        });

        $this->configureRateLimiting();
    }

    /**
     * تكوين حدود الطلبات (Rate Limiting) لحماية النظام من هجمات الحرمان من الخدمة (DoS / DDoS)
     * ومحاولات التخمين والاختراق العنيف (Brute Force) وإغراق الرسائل وقواعد البيانات.
     */
    protected function configureRateLimiting(): void
    {
        // 1. واجهة برمجة التطبيقات العامة (API Global) - 60 طلب/دقيقة
        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (\Illuminate\Http\Request $request, array $headers) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'تم تجاوز الحد الأقصى المسموح للطلبات في الدقيقة. يرجى الانتظار لحظات والمحاولة مجدداً.',
                        'error_code' => 'TOO_MANY_REQUESTS'
                    ], 429, $headers);
                });
        });

        // 2. تطبيق الهاتف المحمول (Mobile API) - 90 طلب/دقيقة
        \Illuminate\Support\Facades\RateLimiter::for('mobile-api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(90)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (\Illuminate\Http\Request $request, array $headers) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'تم تجاوز معدل الطلبات المسموح لتطبيق الهاتف. يرجى الانتظار قليلاً والمحاولة مجدداً.',
                        'error_code' => 'MOBILE_RATE_LIMIT_EXCEEDED'
                    ], 429, $headers);
                });
        });

        // 3. نقاط الدخول والتحقق الحساسة (Strict Auth: Login, Register, OTP, Phone Check) - 5 محاولات/دقيقة
        \Illuminate\Support\Facades\RateLimiter::for('auth-strict', function (\Illuminate\Http\Request $request) {
            $identifier = $request->input('phone') 
                ?? $request->input('email') 
                ?? $request->input('username') 
                ?? '';
            $key = \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower((string) $identifier) . '|' . ($request->ip() ?? 'unknown'));

            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($key)->response(function (\Illuminate\Http\Request $request, array $headers) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'تم تجاوز عدد محاولات الدخول أو التحقق المسموح بها. لحماية حسابك من الاختراق، يرجى الانتظار دقيقة قبل إعادة المحاولة.',
                        'error_code' => 'AUTH_RATE_LIMIT_EXCEEDED'
                    ], 429, $headers);
                }

                return back()
                    ->withInput($request->only('email', 'username', 'phone'))
                    ->withErrors([
                        'email' => 'تم تجاوز عدد المحاولات المسموح بها لحماية أمان النظام. يرجى الانتظار دقيقة والمحاولة مجدداً.'
                    ]);
            });
        });

        // 4. النماذج العامة وإرسال البيانات (Submissions: التبرعات، التطوع، الشكاوى، الحجوزات) - 10 طلبات/دقيقة
        \Illuminate\Support\Facades\RateLimiter::for('submissions', function (\Illuminate\Http\Request $request) {
            $key = ($request->user()?->id ?: $request->ip()) ?? 'unknown';
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($key)->response(function (\Illuminate\Http\Request $request, array $headers) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'تم إرسال عدد كبير من الطلبات في فترة وجيزة. يرجى الانتظار دقيقة قبل إرسال نموذج جديد.',
                        'error_code' => 'SUBMISSION_LIMIT_EXCEEDED'
                    ], 429, $headers);
                }

                return back()->with('error', 'تم إرسال عدد كبير من الطلبات المتتالية. يرجى الانتظار لحظات والمحاولة مجدداً.');
            });
        });

        // 5. العمليات الثقيلة (Heavy Operations: التتبع، التقارير والطباعة، رفع الصور الكثيف) - 25 طلب/دقيقة
        \Illuminate\Support\Facades\RateLimiter::for('heavy-ops', function (\Illuminate\Http\Request $request) {
            $key = ($request->user()?->id ?: $request->ip()) ?? 'unknown';
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(25)->by($key)->response(function (\Illuminate\Http\Request $request, array $headers) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'تم تجاوز معدل العمليات الكثيفة المسموح بها لحماية أداء الخادم. يرجى الانتظار لحظات.',
                        'error_code' => 'HEAVY_OPS_LIMIT_EXCEEDED'
                    ], 429, $headers);
                }

                return back()->with('error', 'الخادم مشغول بمعالجة طلبات متتالية من جهتك. يرجى الانتظار لحظات والمحاولة مجدداً.');
            });
        });

        // 6. تصفح لوحة التحكم والويب العام للحماية من Flooding DoS - 150 طلب/دقيقة
        \Illuminate\Support\Facades\RateLimiter::for('web-global', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(150)->by($request->user()?->id ?: $request->ip());
        });
    }
}

