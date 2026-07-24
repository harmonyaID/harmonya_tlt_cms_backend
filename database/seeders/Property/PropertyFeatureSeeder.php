<?php

namespace Database\Seeders\Property;

use App\Models\Setting\SettingPropertyFeature;
use Illuminate\Database\Seeder;

class PropertyFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            ['name' => 'Bedroom', 'icon' => 'bed', 'hasValue' => true, 'order' => 1],
            ['name' => 'Bathroom', 'icon' => 'bathtub', 'hasValue' => true, 'order' => 2],
            ['name' => 'Guest', 'icon' => 'user', 'hasValue' => true, 'order' => 3],
            ['name' => 'In Villa Dining', 'icon' => 'dining', 'hasValue' => false, 'order' => 4],
            ['name' => 'WIFI', 'icon' => 'wifi', 'hasValue' => false, 'order' => 5],
            ['name' => 'Pool', 'icon' => 'pool', 'hasValue' => false, 'order' => 6],
            ['name' => 'Parking', 'icon' => 'parking', 'hasValue' => false, 'order' => 7],
            ['name' => 'Air Conditioning', 'icon' => 'ac', 'hasValue' => false, 'order' => 8],
        ];

        foreach ($features as $item) {
            SettingPropertyFeature::updateOrCreate(
                ['name' => $item['name']],
                ['icon' => $item['icon'], 'hasValue' => $item['hasValue'], 'order' => $item['order']]
            );
        }
    }
}
