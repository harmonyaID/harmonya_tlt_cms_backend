<?php

namespace App\Services\Guesty;

use App\Models\Setting\SettingAmenity;
use App\Models\Setting\SettingAmenityCategory;

class GuestyAmenityImporter
{
    public function __construct(protected GuestyClient $client)
    {
    }

    /**
     * @return array{categories: int, amenities: int}
     * @throws \Exception
     */
    public function import(): array
    {
        $categoryCount = $this->importCategories();
        $amenityCount = $this->importAmenities();

        return ['categories' => $categoryCount, 'amenities' => $amenityCount];
    }

    private function importCategories(): int
    {
        $response = $this->client->getAmenityGroups();
        $groups = $this->extractList($response);

        $count = 0;
        foreach ($groups as $group) {
            $name = $this->resolveName($group, ['name', 'title', 'group', 'label']);
            if (!$name) {
                continue;
            }

            SettingAmenityCategory::firstOrCreate(['name' => $name]);
            $count++;
        }

        return $count;
    }

    private function importAmenities(): int
    {
        $response = $this->client->getSupportedAmenities();
        $amenities = $this->extractList($response);

        $count = 0;
        foreach ($amenities as $amenity) {
            $name = $this->resolveName($amenity, ['name', 'title', 'label']);
            if (!$name) {
                continue;
            }

            $groupName = is_array($amenity)
                ? $this->pick($amenity, ['group', 'groupName', 'category'])
                : null;

            $categoryId = null;
            if ($groupName) {
                $categoryId = SettingAmenityCategory::firstOrCreate(['name' => $groupName])->id;
            }

            SettingAmenity::updateOrCreate(
                ['name' => $name],
                ['categoryId' => $categoryId, 'isPublish' => true]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Guesty responses may come back as a raw array, or wrapped in {results:[]} / {data:[]}.
     */
    private function extractList(array $response): array
    {
        if (array_is_list($response)) {
            return $response;
        }

        return $response['results'] ?? $response['data'] ?? $response['amenities'] ?? $response['groups'] ?? [];
    }

    /**
     * Resolve a display name whether Guesty returns the item as a plain string
     * (e.g. "Bathroom") or an object (e.g. {"name": "Bathroom"}).
     */
    private function resolveName(mixed $item, array $keys): ?string
    {
        if (is_string($item)) {
            return $this->humanize($item);
        }

        if (is_array($item)) {
            $value = $this->pick($item, $keys);
            return $value ? $this->humanize($value) : null;
        }

        return null;
    }

    /**
     * Turn Guesty's SCREAMING_SNAKE_CASE / snake_case values into "Title Case".
     */
    private function humanize(string $value): string
    {
        if ($value === strtoupper($value) || str_contains($value, '_')) {
            return ucwords(strtolower(str_replace('_', ' ', $value)));
        }

        return $value;
    }

    private function pick(array $item, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (!empty($item[$key]) && is_string($item[$key])) {
                return $item[$key];
            }
        }

        return null;
    }
}