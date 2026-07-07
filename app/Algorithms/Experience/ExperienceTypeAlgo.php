<?php

namespace App\Algorithms\Experience;

use App\Models\Experience\ExperienceType;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExperienceTypeAlgo
{
    public function __construct(protected ExperienceType|int|null $experienceType = null)
    {
        if (is_int($this->experienceType)) {
            $this->experienceType = ExperienceType::find($this->experienceType);
            if (!$this->experienceType) errExperienceTypeGet();
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->experienceType = ExperienceType::create($request->all() + created_by());
                if (!$this->experienceType) errExperienceTypeSave();

                activity()->setCausedBy()->setReference($this->experienceType)
                    ->setType(ActivityType::EXPERIENCE_TYPE)->setAction(ActivityAction::CREATE)
                    ->log("Enter new experience type: " . $this->experienceType->name);
            });

            return success($this->experienceType);
        } catch (\Error $error) { exception($error); }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $this->experienceType->update($request->all());

                activity()->setCausedBy()->setReference($this->experienceType)
                    ->setType(ActivityType::EXPERIENCE_TYPE)->setAction(ActivityAction::UPDATE)
                    ->log("Update experience type: " . $this->experienceType->name);
            });

            return success($this->experienceType);
        } catch (\Error $error) { exception($error); }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {
                if (!$this->experienceType->delete()) errExperienceTypeDelete();

                activity()->setCausedBy()->setReference($this->experienceType)
                    ->setType(ActivityType::EXPERIENCE_TYPE)->setAction(ActivityAction::DELETE)
                    ->log("Delete experience type: " . $this->experienceType->name);
            });

            return success();
        } catch (\Error $error) { exception($error); }
    }
}