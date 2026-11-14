<?php

namespace App\Console\Commands\Migration;

use App\Models\Property\Property;
use App\Services\Property\PropertyPhotoSyncService;
use Illuminate\Console\Command;

class SyncPropertyPhotosCommand extends Command
{
    protected $signature = 'properties:sync-photos
        {--id= : Only sync this Property id}
        {--force : Replace photos the Property already has}';

    protected $description = 'Copy the photos of each Property\'s first variant to the Property itself';

    public function handle(PropertyPhotoSyncService $service)
    {
        $properties = Property::query()
            ->when($this->option('id'), fn ($query, $id) => $query->where('id', $id))
            ->orderBy('id')
            ->get();

        if ($properties->isEmpty()) {
            $this->warn('No property found.');
            return self::SUCCESS;
        }

        $synced = 0;
        $skipped = 0;

        foreach ($properties as $property) {
            $copied = $service->syncFromFirstVariant($property, (bool) $this->option('force'));

            if ($copied > 0) {
                $synced++;
                $this->line("#{$property->id} {$property->nickname}: $copied photos");
            } else {
                $skipped++;
            }
        }

        $this->info("Done. Synced: $synced, skipped: $skipped (already has photos or no variant photos).");

        return self::SUCCESS;
    }
}
