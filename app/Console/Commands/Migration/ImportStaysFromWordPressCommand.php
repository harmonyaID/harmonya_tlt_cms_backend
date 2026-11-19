<?php

namespace App\Console\Commands\Migration;

use App\Services\WordPress\WordPressCsvReader;
use App\Services\WordPress\WordPressStayImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportStaysFromWordPressCommand extends Command
{
    protected $signature = 'wp-import:stays
        {csv? : Path or URL (e.g. a Google Sheet CSV export link) of the "Select Portfolio Export" CSV (default: public/migration/wordpress/select-portfolio-export.csv)}
        {--images= : Folder with the photo files (default: public/migration/wordpress/images)}
        {--download-images : Download photos that are not in the image folder from their WordPress URL}
        {--media= : WP media export (CSV with ID + URL, or JSON {id: url}) to restore images inside descriptions}
        {--dry-run : Only list what would be imported, write nothing}';

    protected $description = 'Import the "Stay - ..." rows of the WordPress "Select Portfolio Export" CSV as parent Properties with source type Bookeasy. Rows that already exist as a Property or Guesty variant are skipped and listed.';

    public function handle(WordPressCsvReader $reader, WordPressStayImporter $importer)
    {
        if ($media = $this->option('media')) {
            if (!is_file($media)) {
                $this->error("Media file not found: {$media}");
                return self::FAILURE;
            }
            $importer->setMedia($reader->readMedia($media));
        }

        $path = $this->csvPath($this->argument('csv') ?: public_path('migration/wordpress/select-portfolio-export.csv'));

        if (!$path) {
            return self::FAILURE;
        }

        $imageDir = $this->option('images') ?: public_path('migration/wordpress/images');
        $download = (bool) $this->option('download-images');
        $dryRun = (bool) $this->option('dry-run');

        $this->info(($dryRun ? '[dry run] ' : '') . 'Importing Stays as Property (' . WordPressStayImporter::SOURCE_TYPE . ') ...');

        $imported = [];
        $existing = [];
        $failed = 0;
        $notStay = 0;

        foreach ($reader->read($path) as $row) {
            $target = $importer->resolve($row);

            if (!$target) {
                $notStay++;
                continue;
            }

            $label = 'WP ' . ($row['ID'] ?? '?') . ' ' . $importer->name($row);

            if ($match = $importer->existing($row)) {
                $existing[] = "{$label}  ->  {$match}";
                continue;
            }

            $where = ($target['type'] ?? 'Stay') . ($target['multiUnit'] ? ', Multi Unit' : '') . ', ' . ($row['Status'] ?? '?');

            if ($dryRun) {
                $imported[] = "{$label}  ({$where})";
                continue;
            }

            try {
                $property = $importer->import($row, $imageDir, $download);
                $imported[] = "{$label}  ->  Property #{$property->id}  ({$where})";
            } catch (\Throwable $e) {
                $failed++;
                $this->error("  ✗ Failed: {$label} - " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info(($dryRun ? 'Would import' : 'Imported') . ': ' . count($imported));
        foreach ($imported as $line) {
            $this->line("  ✓ {$line}");
        }

        $this->newLine();
        $this->warn('Skipped, already exists (check manually): ' . count($existing));
        foreach ($existing as $line) {
            $this->line("  • {$line}");
        }

        if ($importer->missingPhotos) {
            $this->newLine();
            $this->warn(count($importer->missingPhotos) . " photo(s) not found in {$imageDir}. "
                . 'Put the files in that folder (same file names) and re-run, or use --download-images.');
        }

        $this->newLine();
        $this->info("Done. " . ($dryRun ? 'Would import' : 'Imported') . ': ' . count($imported)
            . ', Skipped (exists): ' . count($existing) . ", Failed: {$failed}, Not a Stay: {$notStay}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /** Local path of the CSV; a URL is downloaded to a temporary file first. */
    private function csvPath(string $source): ?string
    {
        if (!preg_match('#^https?://#i', $source)) {
            if (!is_file($source)) {
                $this->error("File not found: {$source}");
                return null;
            }

            return $source;
        }

        $response = Http::timeout(120)->get($source);

        if (!$response->successful() || !str_contains((string) $response->header('Content-Type'), 'csv')) {
            $this->error("Unable to download the CSV from {$source} (HTTP {$response->status()}). Is it shared as \"Anyone with the link\" and an export?format=csv link?");
            return null;
        }

        $path = tempnam(sys_get_temp_dir(), 'wp-stays-') . '.csv';
        file_put_contents($path, $response->body());

        return $path;
    }
}
