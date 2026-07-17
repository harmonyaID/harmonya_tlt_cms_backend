<?php

namespace App\Console\Commands\Migration;

use App\Services\Guesty\GuestyClient;
use App\Services\Guesty\GuestyPropertyImporter;
use Illuminate\Console\Command;

class ImportPropertiesFromGuestyCommand extends Command
{
    protected $signature = 'properties:import-guesty
        {--limit=25 : How many listings to fetch per page}
        {--max= : Stop after importing this many listings total (default: all)}';

    protected $description = 'Pull listings from Guesty Open API and create/update Property + PropertyPhoto records';

    public function handle(GuestyClient $client, GuestyPropertyImporter $importer)
    {
        if (!config('guesty.client_id') || !config('guesty.client_secret')) {
            $this->error('GUESTY_CLIENT_ID / GUESTY_CLIENT_SECRET is not set in .env');
            return self::FAILURE;
        }

        $limit = (int)$this->option('limit');
        $max = $this->option('max') ? (int)$this->option('max') : null;
        $skip = 0;
        $imported = 0;
        $failed = 0;

        $this->info('Starting Guesty import...');

        do {
            try {
                $page = $client->getListings($limit, $skip);
            } catch (\Throwable $e) {
                $this->error('Failed to fetch listings from Guesty: ' . $e->getMessage());
                return self::FAILURE;
            }

            $results = $page['results'] ?? [];
            $total = $page['count'] ?? count($results);

            foreach ($results as $listing) {
                $label = $listing['nickname'] ?? $listing['title'] ?? $listing['_id'];

                try {
                    $importer->import($listing);
                    $imported++;
                    $this->line("  ✓ Imported: {$label}");
                } catch (\Throwable $e) {
                    $failed++;
                    $this->error("  ✗ Failed: {$label} - " . $e->getMessage());
                }

                if ($max && $imported >= $max) {
                    break 2;
                }
            }

            $skip += $limit;

        } while ($skip < $total);

        $this->newLine();
        $this->info("Done. Imported/updated: {$imported}, Failed: {$failed}");

        return self::SUCCESS;
    }
}