<?php

namespace App\Algorithms\Property;

use App\Models\Property\PropertyVariant;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyVariantRelatedAlgo
{
    public function __construct(protected PropertyVariant $property)
    {
    }

    /**
     * Replace the full list of related properties for this property.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function sync(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $ids = collect($request->relatedPropertyVariantIds ?? [])
                    ->reject(fn ($id) => (int)$id === $this->property->id)
                    ->unique()
                    ->values();

                $syncData = [];
                foreach ($ids as $order => $id) {
                    $syncData[$id] = ['order' => $order];
                }

                $this->property->relatedPropertyVariants()->sync($syncData);

                activity()->setCausedBy()
                    ->setReference($this->property)
                    ->setType(ActivityType::PROPERTY_VARIANT)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update related properties for: " . $this->property->nickname);

            });

            return success($this->property->load('relatedPropertyVariants.type', 'relatedPropertyVariants.photos', 'relatedPropertyVariants.addresses'));

        } catch (\Error $error) {
            exception($error);
        }
    }
}
