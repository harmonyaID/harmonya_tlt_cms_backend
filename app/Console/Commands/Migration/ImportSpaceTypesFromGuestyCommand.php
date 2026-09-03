<?php

namespace App\Console\Commands\Migration;

use App\Services\Guesty\GuestyClient;
use App\Services\Guesty\GuestySpaceTypeImporter;
use Illuminate\Console\Command;

class ImportSpaceTypesFromGuestyCommand extends Command
{
    protected $signature = 'property-space-types:import-guesty';

    protected $description = 'Pull room-types and bed-types supported by Guesty Open API into property_room_types / property_bed_types';

    public function handle(GuestyClient $client, GuestySpaceTypeImporter $importer)
    {
        if (!config('guesty.client_id') || !config('guesty.client_secret')) {
            $this->error('GUESTY_CLIENT_ID / GUESTY_CLIENT_SECRET is not set in .env');
            return self::FAILURE;
        }

        $this->info('Fetching room-types & bed-types from Guesty...');

        try {
            $result = $importer->import();
        } catch (\Throwable $e) {
            $this->error('Failed to import space types from Guesty: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info("Done. Room types: {$result['roomTypes']}, Bed types: {$result['bedTypes']}");

        return self::SUCCESS;
    }
}
