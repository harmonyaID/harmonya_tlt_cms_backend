<?php

namespace App\Console\Commands\Migration;

use App\Models\Property\Property;
use App\Services\Property\PropertyPhotoSyncService;
use Illuminate\Console\Command;

class SyncPropertyPhotosCommand extends Command
{
    protected $signature = 'properties:sync-from-variant
        {--id= : Only sync this Property id}
        {--only= : photos, address or features (default: all)}
        {--force : Replace photos / addresses / feature values the Property already has}';

    protected $aliases = ['properties:sync-photos'];

    protected $description = 'Copy the photos and addresses of each Property\'s first variant, and the highest feature values of its variants, to the Property itself';

    public function handle(PropertyPhotoSyncService $service)
    {
        $only = $this->option('only');

        if ($only && !in_array($only, ['photos', 'address', 'features'])) {
            $this->error('--only must be "photos", "address" or "features".');
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
        $featureCount = 0;

        foreach ($properties as $property) {
            $photos = !$only || $only === 'photos' ? $service->syncFromFirstVariant($property, $force) : 0;
            $addresses = !$only || $only === 'address' ? $service->syncAddressesFromFirstVariant($property, $force) : 0;
            $features = !$only || $only === 'features' ? $service->syncFeaturesFromVariants($property, $force) : 0;

            if ($photos || $addresses || $features) {
                $this->line("#{$property->id} {$property->nickname}: $photos photos, $addresses addresses, $features features");
            }

            $photoCount += $photos;
            $addressCount += $addresses;
            $featureCount += $features;
        }

        $this->info("Done. Photos copied: $photoCount, addresses copied: $addressCount, features updated: $featureCount.");

        return self::SUCCESS;
    }
}
