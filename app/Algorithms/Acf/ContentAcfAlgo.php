<?php

namespace App\Algorithms\Acf;

use App\Models\Acf\ContentAcf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ContentAcfAlgo
{
    public function __construct(
        protected mixed $contentable
    ) {
    }

    /**
     * Replace the full set of custom fields for this content record.
     * Existing entries are deleted and rebuilt from the request, matching
     * the create/update pattern already used for tags/amenities/etc in TLT.
     *
     * Expected request shape:
     *   acf: [
     *     { "key": "highlight_video_url", "label": "Highlight Video", "value": "https://..." },
     *     { "key": "extra_note", "label": "Extra Note", "value": "Some free text" },
     *   ]
     *
     * @param Request $request
     *
     * @return Collection<ContentAcf>
     */
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
