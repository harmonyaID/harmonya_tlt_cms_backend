<?php

namespace App\Algorithms\Experience;

use App\Http\Requests\ChangeMailStatusRequest;
use App\Models\Experience\ExperienceInquiryForm;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExperienceInquiryFormAlgo
{
    public function __construct(protected ExperienceInquiryForm|int|null $form = null)
    {
        if (is_int($this->form)) {
            $this->form = ExperienceInquiryForm::find($this->form);
            if (!$this->form) errExperienceInquiryFormGet();
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $this->form = ExperienceInquiryForm::create($request->all());
                if (!$this->form) errExperienceInquiryFormSave();

                activity()->setCausedBy()
                    ->setReference($this->form)
                    ->setType(ActivityType::EXPERIENCE_INQUIRY_FORM)
                    ->setAction(ActivityAction::CREATE)
                    ->log("New inquiry form submitted. Name: " . $this->form->fullName);
            });

            return success($this->form->load('experience'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                if (!$this->form->delete()) errExperienceInquiryFormDelete();

                activity()->setCausedBy()
                    ->setReference($this->form)
                    ->setType(ActivityType::EXPERIENCE_INQUIRY_FORM)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete inquiry form. Name: " . $this->form->fullName);
            });

            return success();
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function changeStatus(ChangeMailStatusRequest $request){
        $this->form->update([
            'statusId' => $request->statusId,
        ]);

        return success($this->form->load('experience'));
    }
}
