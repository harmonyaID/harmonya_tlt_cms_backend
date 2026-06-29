<?php

namespace App\Algorithms\Faq;

use App\Models\Faq\Faq;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FaqAlgo
{
    public function __construct(protected Faq|int|null $faq = null)
    {
        if (is_int($this->faq)) {
            $this->faq = Faq::find($this->faq);
            if (!$this->faq) {
                errFaqGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->faq = Faq::create($request->all());
                if (!$this->faq) errFaqSave();

                activity()->setCausedBy()
                    ->setReference($this->faq)
                    ->setType(ActivityType::FAQ)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Create new FAQ. Question: " . $this->faq->question);
            });

            return success($this->faq);
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->faq->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->faq)
                    ->setType(ActivityType::FAQ)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update FAQ. Question: " . $this->faq->question);
            });

            return success($this->faq);
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                if (!$this->faq->delete()) errFaqDelete();

                activity()->setCausedBy()
                    ->setReference($this->faq)
                    ->setType(ActivityType::FAQ)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete FAQ. Question: " . $this->faq->question);
            });

            return success();
        } catch (\Error $error) {
            exception($error);
        }
    }
}