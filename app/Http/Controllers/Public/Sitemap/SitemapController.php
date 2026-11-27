<?php

namespace App\Http\Controllers\Public\Sitemap;

use App\Http\Controllers\Controller;
use App\Services\Sitemap\SitemapCrawler;
use App\Services\Sitemap\SitemapManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SitemapController extends Controller
{
    /**
     * Getter: link sitemap.xml + info generate terakhir.
     */
    public function get()
    {
        return success(SitemapManager::info());
    }

    /**
     * Isi sitemap.xml hasil scan.
     */
    public function xml()
    {
        $disk = Storage::disk(SitemapCrawler::DISK);
        abort_unless($disk->exists(SitemapCrawler::FILE_PATH), 404);

        return response($disk->get(SitemapCrawler::FILE_PATH), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * FE meminta sitemap dibuat/diperbarui. Scan jalan di background (queue),
     * respons langsung berisi link sitemap. Url yang discan selalu websiteUrl
     * dari website configuration, bukan dari request.
     */
    public function generate(Request $request)
    {
        $token = config('services.sitemap.token');
        if ($token && !hash_equals((string)$token, (string)$request->header('X-Sitemap-Token'))) {
            error(401, 'Invalid sitemap token');
        }

        if (!SitemapManager::websiteUrl()) {
            error(422, 'Website url belum diisi di website configuration');
        }

        $queued = SitemapManager::queue();

        return success(['queued' => $queued] + SitemapManager::info());
    }
}
