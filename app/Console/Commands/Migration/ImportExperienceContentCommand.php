<?php

namespace App\Console\Commands\Migration;

use App\Services\Experience\ExperienceContentDocParser;
use App\Services\Experience\ExperienceContentImporter;
use Illuminate\Console\Command;
use Throwable;

class ImportExperienceContentCommand extends Command
{
    protected $signature = 'experience:import-content
        {path? : Folder with the EAT / PLAY folders of .docx files (default: database/migration-data/experience-content)}
        {--only= : Only the document with this name}
        {--only-empty : Only fill fields that are still empty, keep what is already there}
        {--dry-run : Only show what would change, write nothing}';

    protected $description = 'Apply the experience content documents (.docx in EAT / PLAY folders) to the Experience with the same name and type: description with FAQs, excerpt, SEO title / description, FAQPage schema, and opening hours / address / Instagram / website / WhatsApp / email when those are empty.';

    public function handle(ExperienceContentDocParser $parser)
    {
        $root = rtrim($this->argument('path') ?: database_path('migration-data/experience-content'), '/');

        if (!is_dir($root)) {
            $this->error("Folder not found: {$root}");
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $importer = new ExperienceContentImporter($dryRun, (bool) $this->option('only-empty'));
        $only = $this->option('only');

        $docs = [];
        $empty = [];
        $failed = [];

        foreach ($this->files($root) as $file) {
            $relative = substr($file, strlen($root) + 1);
            $parts = explode('/', $relative);
            $name = pathinfo($file, PATHINFO_FILENAME);

            if ($only && strcasecmp(trim($only), $name) !== 0) {
                continue;
            }

            $type = match (strtoupper($parts[0])) {
                'EAT' => 'Eat',
                'PLAY' => 'Play',
                default => null,
            };

            if (!$type) {
                $failed[] = "{$relative}: not inside an EAT or PLAY folder";
                continue;
            }

            try {
                $doc = $parser->parse($file);
            } catch (Throwable $e) {
                $failed[] = "{$relative}: " . $e->getMessage();
                continue;
            }

            if (!$doc['heading'] && !$doc['intro'] && !$doc['sections']) {
                $empty[] = $relative;
                continue;
            }

            $docs[] = [
                'doc' => $doc,
                'name' => $name,
                'type' => $type,
                'folder' => count($parts) > 2 ? $parts[count($parts) - 2] : '',
                'label' => $relative,
            ];
        }

        $this->info(($dryRun ? '[dry run] ' : '') . 'Experience content from ' . count($docs) . ' document(s)' . ($this->option('only-empty') ? ' (only empty fields)' : ''));

        [$matched, $skipped] = $importer->match($docs);

        foreach ($matched as ['doc' => $item, 'experience' => $experience]) {
            try {
                $changes = $importer->apply($experience, $item['doc']);
            } catch (Throwable $e) {
                $failed[] = "{$item['label']}: " . $e->getMessage();
                continue;
            }

            $this->line(" - {$item['label']} => {$experience->type?->name} #{$experience->id} \"{$experience->name}\"");

            foreach ($changes as $field => $change) {
                $this->line("     {$field}: {$change}");
            }

            if (!$changes) {
                $this->line('     (nothing to change)');
            }

            if (!$item['doc']['metaDesc']) {
                $this->warn('     ! no meta description');
            }
        }

        $this->newLine();
        $this->info(($dryRun ? 'Would update' : 'Updated') . ': ' . count($matched));

        $this->listing('Skipped', $skipped, 'warn');
        $this->listing('Empty documents', $empty, 'warn');
        $this->listing('Failed', $failed, 'error');

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function files(string $root): array
    {
        $files = [];

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if (strtolower($file->getExtension()) === 'docx' && !str_starts_with($file->getFilename(), '~$')) {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        return $files;
    }

    private function listing(string $title, array $items, string $style): void
    {
        if (!$items) {
            return;
        }

        $this->newLine();
        $this->{$style}($title . ': ' . count($items));

        foreach ($items as $item) {
            $this->{$style}(' - ' . $item);
        }
    }
}
