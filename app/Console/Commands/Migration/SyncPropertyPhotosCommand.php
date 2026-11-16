<?php

namespace App\Console\Commands\Migration;

use App\Models\Property\Property;
use App\Services\Property\PropertyPhotoSyncService;
use Illuminate\Console\Command;

class SyncPropertyPhotosCommand extends Command
{
    protected $signature = 'properties:sync-from-variant
        {--id= : Only sync this Property id}
        {--only= : photos or address (default: both)}
        {--force : Replace photos / addresses the Property already has}';

    protected $aliases = ['properties:sync-photos'];

    protected $description = 'Copy the photos and addresses of each Property\'s first variant to the Property itself';

    public function handle(PropertyPhotoSyncService $service)
    {
        $only = $this->option('only');

        if ($only && !in_array($only, ['photos', 'address'])) {
            $this->error('--only must be "photos" or "address".');
            return self::FAILURE;
        }

        $force = (bool) $this->option('force');

        $properties = Property::query()
            ->when($this->option('id'), fn ($query, $id) => $query->where('id', $id))
            ->orderBy('id')
            ->get();

        if ($properties->isEmpty()) {
            $this->warn('No property found.');
            return self::SUCCESS;
        }

        $photoCount = 0;
        $addressCount = 0;

        foreach ($properties as $property) {
            $photos = $only === 'address' ? 0 : $service->syncFromFirstVariant($property, $force);
            $addresses = $only === 'photos' ? 0 : $service->syncAddressesFromFirstVariant($property, $force);

            if ($photos || $addresses) {
                $this->line("#{$property->id} {$property->nickname}: $photos photos, $addresses addresses");
            }

            $photoCount += $photos;
            $addressCount += $addresses;
        }

        $this->info("Done. Photos copied: $photoCount, addresses copied: $addressCount.");

        return self::SUCCESS;
    }
}
