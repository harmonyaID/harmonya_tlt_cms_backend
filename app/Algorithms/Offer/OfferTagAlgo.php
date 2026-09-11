<?php

namespace App\Algorithms\Offer;

use App\Models\Offer\OfferTag;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferTagAlgo
{
    public function __construct(protected OfferTag|int|null $offerTag = null)
    {
        if (is_int($this->offerTag)) {
            $this->offerTag = OfferTag::find($this->offerTag);
            if (!$this->offerTag) {
                errOfferTagGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->offerTag = OfferTag::create($request->all() + created_by());
                if (!$this->offerTag) {
                    errOfferTagSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->offerTag)
                    ->setType(ActivityType::OFFER_TAG)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new offer tag: " . $this->offerTag->name);

            });

            return success($this->offerTag);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->offerTag->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->offerTag)
                    ->setType(ActivityType::OFFER_TAG)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update offer tag: " . $this->offerTag->name);

            });

            return success($this->offerTag);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->offerTag->delete()) {
                    errOfferTagDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->offerTag)
                    ->setType(ActivityType::OFFER_TAG)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete offer tag: " . $this->offerTag->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}
