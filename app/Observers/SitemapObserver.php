<?php

namespace App\Observers;

use App\Services\Sitemap\SitemapManager;
use Illuminate\Database\Eloquent\Model;

/**
 * Memicu scan ulang sitemap ketika konten yang tampil di sitemap berubah:
 * dibuat, dihapus, dipulihkan, atau status tayangnya (draft/aktif), slug, noindex berubah.
 */
class SitemapObserver
{
    /** kolom yang memengaruhi apakah/di mana sebuah halaman tayang */
    private const WATCHED = [
        'isActive', 'statusId', 'status', 'visibility', 'slug', 'startDate', 'endDate',
        'robotIndex', 'websiteUrl',
    ];

    public function saved(Model $model): void
    {
        if ($model->wasRecentlyCreated || $model->wasChanged(self::WATCHED)) {
            $this->trigger();
        }
    }

    public function deleted(Model $model): void
    {
        $this->trigger();
    }

    public function restored(Model $model): void
    {
        $this->trigger();
    }

    private function trigger(): void
    {
        try {
            SitemapManager::queue((int)config('services.sitemap.auto_delay', 60));
        } catch (\Throwable $e) {
            // sitemap tidak boleh menggagalkan simpan konten
            report($e);
        }
    }
}
