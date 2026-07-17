<?php

namespace Database\Seeders\Setting;

use App\Models\Setting\Setting;
use Illuminate\Database\Seeder;

class PropertyNearbySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'name' => 'property_nearby_radius_km',
                'value' => '5',
                'description' => 'Radius (in kilometers) used to find nearby properties around a given property.',
            ],
            [
                'name' => 'property_nearby_limit',
                'value' => '10',
                'description' => 'Maximum number of nearby properties to return.',
            ],
        ];

        foreach ($settings as $item) {
            Setting::updateOrCreate(
                ['name' => $item['name']],
                ['value' => $item['value'], 'description' => $item['description']]
            );
        }
    }
}
