<?php

namespace App\Console\Commands\Migration;

use App\Services\Guesty\GuestyAmenityImporter;
use App\Services\Guesty\GuestyClient;
use Illuminate\Console\Command;

class ImportAmenitiesFromGuestyCommand extends Command
{
    protected $signature = 'amenities:import-guesty';

    protected $description = 'Pull the list of supported amenities & amenity groups from Guesty Open API into setting_amenities / setting_amenity_categories';

    public function handle(GuestyClient $client, GuestyAmenityImporter $importer)
    {
        if (!config('guesty.client_id') || !config('guesty.client_secret')) {
            $this->error('GUESTY_CLIENT_ID / GUESTY_CLIENT_SECRET is not set in .env');
            return self::FAILURE;
        }

        $this->info('Fetching amenities & amenity groups from Guesty...');

        try {
            $result = $importer->import();
        } catch (\Throwable $e) {
            $this->error('Failed to import amenities from Guesty: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info("Done. Categories: {$result['categories']}, Amenities: {$result['amenities']}");

        return self::SUCCESS;
    }
}
