<?php

namespace App\Services\Sitemap;

use App\Jobs\GenerateSitemapJob;
use App\Models\Configuration\WebsiteInformation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SitemapManager
{
    const GENERATING_KEY = 'sitemap:generating';
    const PENDING_KEY = 'sitemap:pending';

    public static function websiteUrl(): ?string
    {
        return optional(WebsiteInformation::first())->websiteUrl ?: null;
    }

    /**
     * Link sitemap.xml di BE (ikut route publik).
     */
    public static function link(): string
    {
        return url('/api/public/sitemap.xml');
    }

    public static function info(): array
    {
        $disk = Storage::disk(SitemapCrawler::DISK);
        $exists = $disk->exists(SitemapCrawler::FILE_PATH);

        return [
            'websiteUrl' => self::websiteUrl(),
            'link' => self::link(),
            'generated' => $exists,
            'generating' => Cache::has(self::GENERATING_KEY),
            'generatedAt' => $exists ? date('c', $disk->lastModified(SitemapCrawler::FILE_PATH)) : null,
            'urlCount' => $exists ? substr_count($disk->get(SitemapCrawler::FILE_PATH), '<url>') : 0,
        ];
    }

    /**
     * Scan sekarang (sinkron). Dipakai oleh job dan command.
     */
    public static function generate(?string $url = null, int $maxPages = 300): array
    {
        $url = $url ?: self::websiteUrl();
        if (!$url) {
            throw new \InvalidArgumentException('Website url belum diisi');
        }

        Cache::put(self::GENERATING_KEY, true, 600);
        try {
            return (new SitemapCrawler($url, $maxPages))->run();
        } finally {
            Cache::forget(self::GENERATING_KEY);
        }
    }

    /**
     * Antrekan scan di background. Return false kalau sudah ada yang menunggu / berjalan.
     */
    public static function queue(int $delaySeconds = 0): bool
    {
        if (!self::websiteUrl()) {
            return false;
        }

        // debounce: banyak perubahan beruntun cukup memicu satu scan
        if (!Cache::add(self::PENDING_KEY, true, $delaySeconds + 600)) {
            return false;
        }

        $job = GenerateSitemapJob::dispatch();
        if ($delaySeconds > 0) {
            $job->delay(now()->addSeconds($delaySeconds));
        }

        return true;
    }
}
