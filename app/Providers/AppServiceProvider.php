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
        // sitemap ikut diperbarui otomatis saat konten publik berubah (mis. jadi draft)
        foreach ([
            \App\Models\Blog\Blog::class,
            \App\Models\Offer\Offer::class,
            \App\Models\Page\Page::class,
            \App\Models\Property\Property::class,
            \App\Models\Experience\Experience::class,
            \App\Models\Boat\Boat::class,
            \App\Models\IslandGuide\IslandGuide::class,
            \App\Models\LembonganArea\LembonganArea::class,
            \App\Models\SEO\ContentSeo::class,
            \App\Models\Configuration\WebsiteInformation::class,
        ] as $model) {
            $model::observe(\App\Observers\SitemapObserver::class);
        }

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(500)->by($request->user()?->id ?: $request->ip());
        });
    }
}
