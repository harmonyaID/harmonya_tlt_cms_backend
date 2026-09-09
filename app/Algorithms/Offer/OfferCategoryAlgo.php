<?php

namespace App\Algorithms\Offer;

use App\Models\Offer\OfferCategory;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferCategoryAlgo
{
    public function __construct(protected OfferCategory|int|null $offerCategory = null)
    {
        if (is_int($this->offerCategory)) {
            $this->offerCategory = OfferCategory::find($this->offerCategory);
            if (!$this->offerCategory) {
                errOfferCategoryGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->offerCategory = OfferCategory::create($request->all() + created_by());
                if (!$this->offerCategory) {
                    errOfferCategorySave();
                }

                activity()->setCausedBy()
                    ->setReference($this->offerCategory)
                    ->setType(ActivityType::OFFER_CATEGORY)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new offer category: " . $this->offerCategory->name);

            });

            return success($this->offerCategory);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->offerCategory->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->offerCategory)
                    ->setType(ActivityType::OFFER_CATEGORY)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update offer category: " . $this->offerCategory->name);

            });

            return success($this->offerCategory);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->offerCategory->delete()) {
                    errOfferCategoryDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->offerCategory)
                    ->setType(ActivityType::OFFER_CATEGORY)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete offer category: " . $this->offerCategory->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}
