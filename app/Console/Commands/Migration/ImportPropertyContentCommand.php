<?php

namespace App\Console\Commands\Migration;

use App\Services\Property\PropertyContentDocParser;
use App\Services\Property\PropertyContentImporter;
use Illuminate\Console\Command;
use Throwable;

class ImportPropertyContentCommand extends Command
{
    protected $signature = 'property:import-content
        {path? : Folder with the property content .docx files (sub folders are read too), or one .docx (default: database/migration-data/property-content)}
        {--only= : Only the document with this title}
        {--only-empty : Only fill fields that are still empty, keep what is already there}
        {--dry-run : Only show what would change, write nothing}';

    protected $description = 'Apply the property content documents (.docx) to the Property with the same name: description, why you\'ll love this, SEO, tags (collection), amenities, bedroom / bathroom / guest features and the resort map as floorplan. Tags and amenities are mapped to the existing ones, missing ones are created.';

    public function handle(PropertyContentDocParser $parser)
    {
        $path = rtrim($this->argument('path') ?: database_path('migration-data/property-content'), '/');
        $files = $this->files($path);

        if (!$files) {
            $this->error("No .docx found in {$path}");
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $importer = new PropertyContentImporter($dryRun, (bool) $this->option('only-empty'));
        $only = $this->option('only');

        $this->info(($dryRun ? '[dry run] ' : '') . 'Property content from ' . count($files) . ' document(s)' . ($this->option('only-empty') ? ' (only empty fields)' : ''));

        $updated = $notFound = $failed = [];

        foreach ($files as $file) {
            try {
                $doc = $parser->parse($file);
            } catch (Throwable $e) {
                $failed[] = basename($file) . ': ' . $e->getMessage();
                continue;
            }

            if ($only && strcasecmp(trim($only), $doc['title']) !== 0) {
                continue;
            }

            [$property, $reason] = $importer->findProperty($doc['title']);

            if (!$property) {
                $notFound[] = "\"{$doc['title']}\" ({$reason})";
                continue;
            }

            try {
                $changes = $importer->apply($property, $doc, $this->images($file, $path));
            } catch (Throwable $e) {
                $failed[] = "\"{$doc['title']}\": " . $e->getMessage();
                continue;
            }

            $updated[] = $doc['title'];
            $this->line(" - \"{$doc['title']}\" => Property #{$property->id} \"{$property->nickname}\"");

            foreach ($changes as $field => $change) {
                $this->line("     {$field}: {$change}");
            }

            foreach ($this->warnings($doc) as $warning) {
                $this->warn("     ! {$warning}");
            }
        }

        $this->newLine();
        $this->info(($dryRun ? 'Would update' : 'Updated') . ': ' . count($updated));

        $this->listing(($dryRun ? 'New tags (would be created)' : 'New tags created'), $importer->createdTags);
        $this->listing(($dryRun ? 'New amenities (would be created)' : 'New amenities created'), $importer->createdAmenities);
        $this->listing('Not found (skipped)', $notFound, 'warn');
        $this->listing('Failed', $failed, 'error');

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function files(string $path): array
    {
        if (is_file($path)) {
            return [$path];
        }

        if (!is_dir($path)) {
            return [];
        }

        $files = [];

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if (strtolower($file->getExtension()) === 'docx' && !str_starts_with($file->getFilename(), '~$')) {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        return $files;
    }

    /** Images next to the document, only when it has its own sub folder. */
    private function images(string $file, string $root): array
    {
        $dir = dirname($file);

        if ($dir === $root || is_file($root)) {
            return [];
        }

        return array_values(array_filter(glob($dir . '/*'), fn ($image) => preg_match('/\.(jpe?g|png|webp)$/i', $image)));
    }

    /** Things in the document the content team should check. */
    private function warnings(array $doc): array
    {
        $warnings = [];

        if (!$doc['metaTitle'] || !$doc['metaDesc']) {
            $warnings[] = 'no meta title / meta desc';
        }

        if (collect($doc['why'])->contains(fn ($item) => !$item['title'])) {
            $warnings[] = 'why you\'ll love this item without title (skipped)';
        }

        // summary line "6 bedrooms | Up to 18 guests" vs the Features section
        $summary = strtolower((string) $doc['summary']);
        $features = strtolower(implode(' | ', $doc['features']));

        foreach (['bedroom' => '/([\d\s,&and]+?)\s*bedrooms?/', 'guest' => '/up to\s+([\d\s,&and]+?)\s*guests?/'] as $name => $pattern) {
            if (preg_match($pattern, $summary, $a) && preg_match($pattern, $features, $b)
                && preg_replace('/\D+/', ' ', trim($a[1])) !== preg_replace('/\D+/', ' ', trim($b[1]))) {
                $warnings[] = "{$name}s: summary says \"" . trim($a[1]) . "\", features say \"" . trim($b[1]) . "\" (features used)";
            }
        }

        return $warnings;
    }

    private function listing(string $title, array $items, string $style = 'line'): void
    {
        if (!$items) {
            return;
        }

        $this->newLine();
        $this->{$style === 'line' ? 'info' : $style}($title . ': ' . count($items));

        foreach ($items as $item) {
            $this->{$style}(' - ' . $item);
        }
    }
}
