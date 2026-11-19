<?php

namespace App\Providers;

use App\Services\Notification\MailProviderSettings;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MailProviderSettings::class);

        // Mail provider from Settings > Notification Credentials, read the first time mail is used.
        $this->app->afterResolving('mail.manager', fn () => $this->app->make(MailProviderSettings::class)->apply());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(500)->by($request->user()?->id ?: $request->ip());
        });
    }
}
