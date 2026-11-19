<?php

namespace App\Console\Commands\Migration;

use App\Models\Blog\Blog;
use App\Services\Constant\Storage\PathConstant;
use App\Services\WordPress\WordPressBlogImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportBlogsFromWordPressCommand extends Command
{
    protected $signature = 'blogs:import-wordpress
        {--url=https://www.thelembongantraveller.com : Base URL of the WordPress site}
        {--per-page=5 : How many posts to fetch per page (max 5)}
        {--start-page=1 : Page to start from, to resume a stopped import}
        {--max= : Stop after importing this many posts total (default: all)}
        {--delay=500 : Pause in milliseconds before every request to WordPress}
        {--tries=3 : Attempts per request on timeout, connection error, 429 or 5xx}
        {--missing-photos : Only download missing thumbnails / SEO images of blogs already imported}';

    protected $description = 'Pull blog posts from a WordPress site\'s public REST API and import/update them as Blog records';

    private WordPressBlogImporter $importer;

    public function handle()
    {
        $baseUrl = $this->option('url');
        $perPage = min(max((int) $this->option('per-page'), 1), 5);
        $page = max((int) $this->option('start-page'), 1);
        $max = $this->option('max') ? (int) $this->option('max') : null;
        $photosOnly = (bool) $this->option('missing-photos');

        $this->importer = new WordPressBlogImporter(
            $baseUrl,
            max((int) $this->option('delay'), 0),
            max((int) $this->option('tries'), 1)
        );

        $this->info(($photosOnly ? 'Downloading missing photos' : 'Importing blog posts') . " from {$baseUrl} ...");

        $imported = 0;
        $skipped = 0;
        $photoFailures = [];

        while (true) {
            $this->line('');
            $this->info("== Page {$page} (per_page={$perPage}) ==");

            $t = microtime(true);

            try {
                $posts = $this->importer->fetchPage($page, $perPage);
            } catch (\Throwable $e) {
                $this->error("Failed to fetch page {$page}: " . $e->getMessage());
                $this->line($this->resumeCommand($page, $perPage, $photosOnly));
                return self::FAILURE;
            }

            $this->line('   fetched ' . count($posts) . ' posts in ' . round(microtime(true) - $t, 2) . 's');

            if (empty($posts)) {
                break;
            }

            foreach ($posts as $i => $post) {
                if ($max && $imported >= $max) {
                    break 2;
                }

                $slug = $post['slug'] ?? '?';
                $title = html_entity_decode(strip_tags($post['title']['rendered'] ?? ''), ENT_QUOTES);
                $number = $imported + $skipped + 1;

                $this->line("[{$number}] (page {$page}, " . ($i + 1) . '/' . count($posts) . ") {$title}");
                $this->line("     slug: {$slug}");

                $start = microtime(true);

                try {
                    if ($photosOnly) {
                        $blog = Blog::where('slug', $slug)->first();

                        if (!$blog) {
                            $this->warn('     [SKIP] not imported yet');
                            $skipped++;
                            continue;
                        }
                    } else {
                        $this->line('     - importing post...');
                        $blog = $this->importer->importPost($post);
                        $this->line('     - post done (' . round(microtime(true) - $start, 2) . 's)');

                        $this->line('     - importing SEO...');
                        $this->importSeo($blog, $post);
                    }

                    $thumbnailOk = $this->importer->syncThumbnail($blog, $post);
                    $seoImageOk = $this->syncSeoThumbnail($blog, $post);

                    if (!$thumbnailOk || !$seoImageOk) {
                        $photoFailures[] = $slug;
                        $this->warn('     [PHOTO] ' . implode(', ', array_filter([
                            $thumbnailOk ? null : 'thumbnail',
                            $seoImageOk ? null : 'SEO image',
                        ])) . ' failed to download');
                    }

                    $imported++;
                    $this->info('     OK total ' . round(microtime(true) - $start, 2) . 's');
                } catch (\Throwable $e) {
                    $this->warn("     [SKIP] {$slug} - " . $e->getMessage());
                    $skipped++;
                }
            }

            $page++;
        }

        $this->info(($photosOnly ? 'Done. Checked: ' : 'Done. Imported/updated: ') . "{$imported}, Skipped: {$skipped}");

        if ($photoFailures) {
            $this->warn('Photos still missing for ' . count($photoFailures) . ' blog(s): ' . implode(', ', $photoFailures));
            $this->line('Run again to retry only the missing photos: php artisan blogs:import-wordpress --missing-photos --delay=2000');
        }

        return self::SUCCESS;
    }

    private function resumeCommand(int $page, int $perPage, bool $photosOnly): string
    {
        return 'Resume with: php artisan blogs:import-wordpress --per-page=' . $perPage
            . ' --start-page=' . $page
            . ($photosOnly ? ' --missing-photos' : '');
    }

    /**
     * Save the Yoast SEO fields. The SEO image is handled by syncSeoThumbnail(),
     * so an existing image is never replaced by an empty one.
     */
    private function importSeo(Blog $blog, array $post): void
    {
        $yoast = $post['yoast_head_json'] ?? [];

        if (empty($yoast)) {
            return;
        }

        $robots = $yoast['robots'] ?? [];

        $blog->seo()->updateOrCreate(
            [
                'contentableId' => $blog->id,
                'contentableType' => $blog->getMorphClass(),
            ],
            [
                'info' => null,
                'title' => $yoast['title'] ?? $blog->title,
                'slug' => $blog->slug,
                'description' => $yoast['description'] ?? null,
                'metaKeyword' => null,
                'canonicalUrl' => $yoast['canonical'] ?? null,
                'robotIndex' => ($robots['index'] ?? 'index') === 'index',
                'robotFollow' => ($robots['follow'] ?? 'follow') === 'follow',
                'schemaMarkup' => $yoast['schema'] ?? null,
            ]
        );
    }

    /**
     * Download the Yoast og:image when the blog's SEO has no thumbnail yet.
     * Returns false when the post has an image that could not be downloaded.
     */
    private function syncSeoThumbnail(Blog $blog, array $post): bool
    {
        $url = $post['yoast_head_json']['og_image'][0]['url'] ?? null;
        $seo = $blog->seo()->first();

        if (!$url || !$seo || $seo->thumbnail) {
            return true;
        }

        $filename = $this->importer->downloadImage(
            $url,
            PathConstant::IMAGES_SEO_STORAGE_PUBLIC_PATH(),
            Str::slug($blog->slug ?: $blog->title) . '-seo'
        );

        if (!$filename) {
            return false;
        }

        $seo->update(['thumbnail' => $filename]);

        return true;
    }
}
