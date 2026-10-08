<?php

namespace App\Console\Commands\Migration;

use App\Services\WordPress\WordPressBlogImporter;
use Illuminate\Console\Command;

class ImportBlogsFromWordPressCommand extends Command
{
    protected $signature = 'blogs:import-wordpress
        {--url=https://www.thelembongantraveller.com : Base URL of the WordPress site}
        {--per-page=20 : How many posts to fetch per page}
        {--max= : Stop after importing this many posts total (default: all)}';

    protected $description = 'Pull blog posts from a WordPress site\'s public REST API and import/update them as Blog records';

    public function handle()
    {
        $baseUrl = $this->option('url');
        $perPage = (int)$this->option('per-page');
        $max = $this->option('max') ? (int)$this->option('max') : null;

        $importer = new WordPressBlogImporter($baseUrl);

        $this->info("Importing blog posts from {$baseUrl} ...");

        $page = 1;
        $imported = 0;
        $skipped = 0;

        while (true) {
            $this->line("");
            $this->info("== Page {$page} (per_page={$perPage}) ==");

            $t = microtime(true);

            try {
                $posts = $importer->fetchPage($page, $perPage);
            } catch (\Throwable $e) {
                $this->error("Failed to fetch page {$page}: " . $e->getMessage());
                $this->line('Resume dengan: php artisan blogs:import-wordpress --per-page=' . $perPage . ' --start-page=' . $page);
                return self::FAILURE;
            }

            $this->line("   fetched " . count($posts) . " posts in " . round(microtime(true) - $t, 2) . "s");

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

                $this->line("[{$number}] (page {$page}, " . ($i + 1) . "/" . count($posts) . ") {$title}");
                $this->line("     slug: {$slug}");

                $start = microtime(true);

                try {
                    $this->line("     - importing post...");
                    $blog = $importer->importPost($post);
                    $this->line("     - post done (" . round(microtime(true) - $start, 2) . "s)");

                    $seoStart = microtime(true);
                    $this->line("     - importing SEO...");
                    $this->importSeo($blog, $post);
                    $this->line("     - SEO done (" . round(microtime(true) - $seoStart, 2) . "s)");

                    $imported++;
                    $this->info("     OK total " . round(microtime(true) - $start, 2) . "s");
                } catch (\Throwable $e) {
                    $this->warn("     [SKIP] {$slug} - " . $e->getMessage());
                    $skipped++;
                }
            }

            $page++;
        }

        $this->info("Done. Imported/updated: {$imported}, Skipped: {$skipped}");

        return self::SUCCESS;
    }

    private function importSeo($blog, array $post): void
    {
        $yoast = $post['yoast_head_json'] ?? [];

        if (empty($yoast)) {
            return;
        }

        $robots = $yoast['robots'] ?? [];

        $thumbnail = null;

        if (!empty($yoast['og_image'][0]['url'])) {
            $thumbnail = $this->downloadSeoImage(
                $yoast['og_image'][0]['url'],
                $blog
            );
        }

        $blog->seo()->updateOrCreate(
            [
                'contentableId' => $blog->id,
                'contentableType' => $blog->getMorphClass(),
            ],
            [
                'info' => null,

                'title' => $yoast['title']
                    ?? $blog->title,

                'slug' => $blog->slug,

                'description' => $yoast['description']
                    ?? null,

                'metaKeyword' => null,

                'thumbnail' => $thumbnail,

                'canonicalUrl' => $yoast['canonical']
                    ?? null,

                'robotIndex' => ($robots['index'] ?? 'index') === 'index',

                'robotFollow' => ($robots['follow'] ?? 'follow') === 'follow',

                'schemaMarkup' => $yoast['schema'] ?? null,
            ]
        );
    }

    private function downloadSeoImage(string $url, $blog): ?string
    {
        try {
            $contents = file_get_contents($url);

            if ($contents === false) {
                return null;
            }

            $path = PathConstant::IMAGES_SEO_STORAGE_PUBLIC_PATH();

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $extension = pathinfo(
                parse_url($url, PHP_URL_PATH),
                PATHINFO_EXTENSION
            );

            $extension = $extension ?: 'jpg';

            $filename = filenameFromString(
                $blog->title . '-seo'
            ) . '.' . $extension;

            file_put_contents(
                $path . $filename,
                $contents
            );

            return $filename;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
