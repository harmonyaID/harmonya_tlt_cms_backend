<?php

namespace App\Services\Guesty;

use App\Models\Property\PropertyBedType;
use App\Models\Property\PropertyRoomType;

class GuestySpaceTypeImporter
{
    public function __construct(protected GuestyClient $client)
    {
    }

    /**
     * @return array{roomTypes: int, bedTypes: int}
     * @throws \Exception
     */
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

    /**
     * Guesty responses may come back as a raw array of strings, a raw array of objects,
     * or wrapped in {results:[]} / {data:[]}.
     */
    private function extractList(array $response): array
    {
        if (array_is_list($response)) {
            return $response;
        }

        return $response['results'] ?? $response['data'] ?? $response['types'] ?? [];
    }

    /**
     * Each item might be a plain string (e.g. "KING_BED") or an object with name/title/label/type.
     */
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

    /**
     * Turn Guesty's SCREAMING_SNAKE_CASE enum values (e.g. KING_BED) into "King Bed".
     */
    private function humanize(string $value): string
    {
        if ($value === strtoupper($value) && str_contains($value, '_')) {
            return ucwords(strtolower(str_replace('_', ' ', $value)));
        }

        return $value;
    }
}
