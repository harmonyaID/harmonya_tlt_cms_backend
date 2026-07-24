<?php

namespace App\Algorithms\Property;

use App\Models\Property\Property;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyRelatedAlgo
{
    public function __construct(protected Property $property)
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

                $ids = collect($request->relatedPropertyIds ?? [])
                    ->reject(fn ($id) => (int)$id === $this->property->id)
                    ->unique()
                    ->values();

                $syncData = [];
                foreach ($ids as $order => $id) {
                    $syncData[$id] = ['order' => $order];
                }

                $this->property->relatedProperties()->sync($syncData);

                activity()->setCausedBy()
                    ->setReference($this->property)
                    ->setType(ActivityType::PROPERTY)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update related properties for: " . $this->property->nickname);

            });

            return success($this->property->load('relatedProperties.type', 'relatedProperties.photos', 'relatedProperties.addresses'));

        } catch (\Error $error) {
            exception($error);
        }
    }
}
