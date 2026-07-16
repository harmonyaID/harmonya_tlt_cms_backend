<?php

namespace App\Algorithms\Property;

use App\Models\Property\PropertyTag;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyTagAlgo
{
    public function __construct(protected PropertyTag|int|null $propertyTag= null)
    {
        if (is_int($this->propertyTag)) {
            $this->propertyTag = PropertyTag::find($this->propertyTag);
            if (!$this->propertyTag) {
                errPropertyTagGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->propertyTag = PropertyTag::create($request->all() + created_by());
                if (!$this->propertyTag) {
                    errPropertyTagSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertyTag)
                    ->setType(ActivityType::PROPERTY_TYPE)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new property type: " . $this->propertyTag->name);

            });

            return success($this->propertyTag);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->propertyTag->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->propertyTag)
                    ->setType(ActivityType::PROPERTY_TYPE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update property type: " . $this->propertyTag->name);

            });

            return success($this->propertyTag);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->propertyTag->delete()) {
                    errPropertyTagDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->propertyTag)
                    ->setType(ActivityType::PROPERTY_TYPE)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete property type: " . $this->propertyTag->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}