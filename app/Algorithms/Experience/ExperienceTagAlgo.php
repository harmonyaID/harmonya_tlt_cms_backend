<?php

namespace App\Algorithms\Experience;

use App\Models\Experience\ExperienceTag;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExperienceTagAlgo
{
    public function __construct(
        protected ExperienceTag|int|null $experienceTag = null
    ) {
        if (is_int($this->experienceTag)) {
            $this->experienceTag = ExperienceTag::find($this->experienceTag);

            if (!$this->experienceTag) {
                errExperienceTagGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->experienceTag = ExperienceTag::create(
                    $request->all() + created_by()
                );

                if (!$this->experienceTag) {
                    errExperienceTagSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->experienceTag)
                    ->setType(ActivityType::EXPERIENCE_TAG)
                    ->setAction(ActivityAction::CREATE)
                    ->log(
                        "Enter new experience tag: " .
                        $this->experienceTag->name
                    );
            });

            return success($this->experienceTag);
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->experienceTag->update(
                    $request->all()
                );

                activity()->setCausedBy()
                    ->setReference($this->experienceTag)
                    ->setType(ActivityType::EXPERIENCE_TAG)
                    ->setAction(ActivityAction::UPDATE)
                    ->log(
                        "Update experience tag: " .
                        $this->experienceTag->name
                    );
            });

            return success($this->experienceTag);
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                if (!$this->experienceTag->delete()) {
                    errExperienceTagDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->experienceTag)
                    ->setType(ActivityType::EXPERIENCE_TAG)
                    ->setAction(ActivityAction::DELETE)
                    ->log(
                        "Delete experience tag: " .
                        $this->experienceTag->name
                    );
            });

            return success();
        } catch (\Error $error) {
            exception($error);
        }
    }
}