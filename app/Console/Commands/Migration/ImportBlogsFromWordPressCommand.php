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
            try {
                $posts = $importer->fetchPage($page, $perPage);
            } catch (\Throwable $e) {
                $this->error("Failed to fetch page {$page}: " . $e->getMessage());
                $this->line('');
                $this->line('If this is a 404 or connection error, the WordPress REST API may be disabled');
                $this->line('on this site (some security plugins turn it off). In that case, use');
                $this->line('WordPress\'s built-in exporter instead: wp-admin > Tools > Export > All content,');
                $this->line('which downloads a WXR (XML) file you can send me to write a one-off importer for.');
                return self::FAILURE;
            }

            if (empty($posts)) {
                break;
            }

            foreach ($posts as $post) {
                if ($max && $imported >= $max) {
                    break 2;
                }

                try {
                    $blog = $importer->importPost($post);
                    $this->line("  [OK] {$blog->slug}");
                    $imported++;
                } catch (\Throwable $e) {
                    $this->warn("  [SKIP] " . ($post['slug'] ?? '?') . " - " . $e->getMessage());
                    $skipped++;
                }
            }

            $page++;
        }

        $this->info("Done. Imported/updated: {$imported}, Skipped: {$skipped}");

        return self::SUCCESS;
    }
}
