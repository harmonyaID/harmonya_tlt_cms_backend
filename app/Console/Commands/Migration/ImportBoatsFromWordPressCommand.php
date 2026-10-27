<?php

namespace App\Console\Commands\Migration;

use App\Services\WordPress\WordPressBoatImporter;
use App\Services\WordPress\WordPressCsvReader;
use Illuminate\Console\Command;

class ImportBoatsFromWordPressCommand extends Command
{
    protected $signature = 'wp-import:boats
        {csv? : Path to the "Pages Export" CSV (default: public/migration/wordpress/pages-export.csv)}
        {--images= : Folder with the thumbnail files (default: public/migration/wordpress/images)}
        {--download-images : Download thumbnails that are not in the image folder from their WordPress URL}
        {--media= : WP media export (CSV with ID + URL, or JSON {id: url}) to restore images inside descriptions}
        {--without-description : Do not import the description (leave it empty)}
        {--dry-run : Only list what would be imported, write nothing}';

    protected $description = 'Import the WordPress "Pages Export" CSV into Boat (creating the Boat Type "Boat Transfer" / "Private Boat" when missing). Only rows labelled "Boat - <Type>" in the TLT Migration List are imported. Safe to re-run: existing rows are matched by slug and updated.';

    public function handle(WordPressCsvReader $reader, WordPressBoatImporter $importer)
    {
        if ($media = $this->option('media')) {
            if (!is_file($media)) {
                $this->error("Media file not found: {$media}");
                return self::FAILURE;
            }
            $importer->setMedia($reader->readMedia($media));
        }

        $path = $this->argument('csv') ?: public_path('migration/wordpress/pages-export.csv');
        $imageDir = $this->option('images') ?: public_path('migration/wordpress/images');
        $dryRun = (bool) $this->option('dry-run');
        $withDescription = !$this->option('without-description');
        $download = (bool) $this->option('download-images');

        if (!is_file($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $this->info(($dryRun ? '[dry run] ' : '') . "Importing Boats from {$path} ...");

        $imported = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($reader->read($path) as $row) {
            $target = $importer->resolve($row);

            if (!$target) {
                $skipped++;
                continue;
            }

            $label = html_entity_decode($row['Title'] ?? '#' . ($row['ID'] ?? '?'), ENT_QUOTES);
            $where = $target['type'];

            if ($dryRun) {
                $imported++;
                $this->line("  • {$label}  ->  {$where}");
                continue;
            }

            try {
                $importer->import($row, $imageDir, $withDescription, $download);
                $imported++;
                $this->line("  ✓ Imported: {$label}  ->  {$where}");
            } catch (\Throwable $e) {
                $failed++;
                $this->error("  ✗ Failed: {$label} - " . $e->getMessage());
            }
        }

        $this->newLine();

        if ($importer->missingThumbnails) {
            $this->warn(count($importer->missingThumbnails) . " thumbnail(s) not found in {$imageDir}. "
                . 'Put the files in that folder (same file names) and re-run, or use --download-images.');
        }

        $this->info(($dryRun ? 'Dry run. Would import: ' : 'Done. Imported/updated: ') . "{$imported}, Skipped (not a Boat): {$skipped}, Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}