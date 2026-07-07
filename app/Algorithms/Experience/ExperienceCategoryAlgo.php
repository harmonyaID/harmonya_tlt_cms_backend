<?php

namespace App\Algorithms\Experience;

use App\Models\Experience\ExperienceCategory;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExperienceCategoryAlgo
{
    public function __construct(protected ExperienceCategory|int|null $experienceCategory = null)
    {
        if (is_int($this->experienceCategory)) {
            $this->experienceCategory = ExperienceCategory::find($this->experienceCategory);
            if (!$this->experienceCategory) errExperienceCategoryGet();
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->experienceCategory = ExperienceCategory::create($request->all() + created_by());
                if (!$this->experienceCategory) errExperienceCategorySave();

                activity()->setCausedBy()->setReference($this->experienceCategory)
                    ->setType(ActivityType::EXPERIENCE_CATEGORY)->setAction(ActivityAction::CREATE)
                    ->log("Enter new experience category: " . $this->experienceCategory->name);
            });

            return success($this->experienceCategory->load('type'));
        } catch (\Error $error) { exception($error); }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->experienceCategory->update($request->all());

                activity()->setCausedBy()->setReference($this->experienceCategory)
                    ->setType(ActivityType::EXPERIENCE_CATEGORY)->setAction(ActivityAction::UPDATE)
                    ->log("Update experience category: " . $this->experienceCategory->name);
            });

            return success($this->experienceCategory->load('type'));
        } catch (\Error $error) { exception($error); }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {
                if (!$this->experienceCategory->delete()) errExperienceCategoryDelete();

                activity()->setCausedBy()->setReference($this->experienceCategory)
                    ->setType(ActivityType::EXPERIENCE_CATEGORY)->setAction(ActivityAction::DELETE)
                    ->log("Delete experience category: " . $this->experienceCategory->name);
            });

            return success();
        } catch (\Error $error) { exception($error); }
    }
}