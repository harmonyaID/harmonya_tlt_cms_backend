<?php

namespace App\Algorithms\Faq;

use App\Models\Faq\FaqType;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FaqTypeAlgo
{
    public function __construct(protected FaqType|int|null $faqType = null)
    {
        if (is_int($this->faqType)) {
            $this->faqType = FaqType::find($this->faqType);
            if (!$this->faqType) {
                errFaqTypeGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->faqType = FaqType::create($request->all() + created_by());
                if (!$this->faqType) {
                    errFaqTypeSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->faqType)
                    ->setType(ActivityType::FAQ_TYPE)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Create new FAQ Type: " . $this->faqType->name);
            });

            return success($this->faqType);
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->faqType->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->faqType)
                    ->setType(ActivityType::FAQ_TYPE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update FAQ Type: " . $this->faqType->name);
            });

            return success($this->faqType);
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                if (!$this->faqType->delete()) {
                    errFaqTypeDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->faqType)
                    ->setType(ActivityType::FAQ_TYPE)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete FAQ Type: " . $this->faqType->name);
            });

            return success();
        } catch (\Error $error) {
            exception($error);
        }
    }
}
