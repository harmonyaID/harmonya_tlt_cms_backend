<?php

namespace Database\Seeders\Configuration;

use App\Models\Configuration\WebsiteInformation;
use Illuminate\Database\Seeder;

class WebsiteInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WebsiteInformation::truncate();

        WebsiteInformation::create([
            'title' => 'TLT Property',
            'email' => '',
            'phone' => '',
            'fax' => '',
            'whatsapp' => '',
            'country' => 'id',
            'postalCode' => '',
            'address' => '',
            'mapEmbed' => '',
            'socialMedia' => array([
                'key' => '',
                'name' => '',
                'icon' => '',
                'link' => '',
            ])
        ]);
    }

}