<?php

namespace Database\Seeders\Setting;

use App\Models\Setting\SettingCountry;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = $this->getAllCountries();

        foreach ($countries as $item) {

            $cca2 = $item['codes']['alpha_2'] ?? null;
            $cca3 = $item['codes']['alpha_3'] ?? null;
            $ccn3 = $item['codes']['ccn3'] ?? null;

            if (SettingCountry::where('cca2', $cca2)->where('cca3', $cca3)->exists()) {
                continue;
            }

            SettingCountry::updateOrCreate(
                [
                    'cca2' => $cca2,
                ],
                [
                    'cca3'      => $cca3,
                    'ccn3'      => $ccn3,
                    'name'      => $item['names']['common'] ?? null,
                    'flag'      => $item['flag']['emoji'] ?? null,
                    // atau pakai SVG:
                    // 'flag' => $item['flag']['url_svg'] ?? null,

                    'phoneCode' => !empty($item['calling_codes'])
                        ? '+' . $item['calling_codes'][0]
                        : null,
                ]
            );
        }

        $this->command->info('Countries imported: ' . count($countries));
    }

    /**
     * Get all countries from API
     */
    private function getAllCountries(): array
    {
        $allCountries = [];
        $offset = 0;
        $limit = 100;

        do {
            $response = $this->getData($offset, $limit);

            $allCountries = array_merge(
                $allCountries,
                $response['data']['objects'] ?? []
            );

            $meta = $response['data']['meta'] ?? [];

            $offset += $limit;

        } while (($meta['more'] ?? false) === true);

        return $allCountries;
    }

    /**
     * Get paginated data
     */
    private function getData(int $offset = 0, int $limit = 100): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.restcountries.com/countries/v5?limit={$limit}&offset={$offset}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer rc_live_00b7a84f1749458a9880991cc4568306',
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new \Exception(curl_error($curl));
        }

        curl_close($curl);

        return json_decode($response, true);
    }
}