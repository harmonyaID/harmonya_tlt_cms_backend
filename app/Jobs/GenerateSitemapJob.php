<?php

namespace App\Jobs;

use App\Services\Sitemap\SitemapManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class GenerateSitemapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $timeout = 300;
    public int $tries = 1;

    public function handle(): void
    {
        // izinkan perubahan berikutnya memicu scan baru
        Cache::forget(SitemapManager::PENDING_KEY);

        if (SitemapManager::websiteUrl()) {
            SitemapManager::generate();
        }
    }

    public function failed(\Throwable $e): void
    {
        Cache::forget(SitemapManager::PENDING_KEY);
        Cache::forget(SitemapManager::GENERATING_KEY);
    }
}
