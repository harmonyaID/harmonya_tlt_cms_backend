<?php

namespace App\Services\Guesty;

use App\Models\Property\PropertyBedType;
use App\Models\Property\PropertyRoomType;

class GuestySpaceTypeImporter
{
    public function __construct(protected GuestyClient $client)
    {
    }

    public function import(): array
    {
        return [
            'roomTypes' => $this->importRoomTypes(),
            'bedTypes' => $this->importBedTypes(),
        ];
    }

    private function importRoomTypes(): int
    {
        $response = $this->client->getRoomTypes();
        $items = $this->extractList($response);

        $count = 0;
        foreach ($items as $item) {
            $name = $this->resolveName($item);
            if (!$name) {
                continue;
            }

            PropertyRoomType::firstOrCreate(['name' => $name]);
            $count++;
        }

        return $count;
    }

    private function importBedTypes(): int
    {
        $response = $this->client->getBedTypes();
        $items = $this->extractList($response);

        $count = 0;
        foreach ($items as $item) {
            $name = $this->resolveName($item);
            if (!$name) {
                continue;
            }

            PropertyBedType::firstOrCreate(['name' => $name]);
            $count++;
        }

        return $count;
    }

    private function extractList(array $response): array
    {
        if (array_is_list($response)) {
            return $response;
        }

        return $response['results'] ?? $response['data'] ?? $response['types'] ?? [];
    }

    private function resolveName(mixed $item): ?string
    {
        if (is_string($item)) {
            return $this->humanize($item);
        }

        if (is_array($item)) {
            foreach (['name', 'title', 'label', 'type'] as $key) {
                if (!empty($item[$key]) && is_string($item[$key])) {
                    return $this->humanize($item[$key]);
                }
            }
        }

        return null;
    }

    private function humanize(string $value): string
    {
        if ($value === strtoupper($value) && str_contains($value, '_')) {
            return ucwords(strtolower(str_replace('_', ' ', $value)));
        }

        return $value;
    }
}
