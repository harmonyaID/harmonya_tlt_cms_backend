<?php

namespace App\Algorithms\Acf;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ContentAcfAlgo
{
    public function __construct(
        protected mixed $contentable
    ) {
    }

    public function save(Request $request): Collection
    {
        $items = $request->input('acf', []);

        $this->contentable->acf()->delete();

        if (empty($items) || !is_array($items)) {
            return collect();
        }

        $created = collect();
        foreach (array_values($items) as $order => $item) {
            $created->push(
                $this->contentable->acf()->create([
                    'key' => $item['key'] ?? null,
                    'label' => $item['label'] ?? null,
                    'value' => $item['value'] ?? null,
                    'order' => $order,
                ])
            );
        }

        return $created;
    }
}
