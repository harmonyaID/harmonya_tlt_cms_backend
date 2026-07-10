<?php

namespace App\Algorithms\Boat;

use App\Http\Requests\ChangeMailStatusRequest;
use App\Models\Boat\BoatContactForm;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoatContactFormAlgo
{
    public function __construct(protected BoatContactForm|int|null $contactForm = null)
    {
        if (is_int($this->contactForm)) {
            $this->contactForm = BoatContactForm::find($this->contactForm);
            if (!$this->contactForm) {
                errBoatContactFormGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->contactForm = BoatContactForm::create($request->all());
                if (!$this->contactForm) {
                    errBoatContactFormSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->contactForm)
                    ->setType(ActivityType::BOAT_CONTACT_FORM)
                    ->setAction(ActivityAction::CREATE)
                    ->log("New boat booking form submitted. Name: " . $this->contactForm->name);
            });

            return success($this->contactForm->load('boat'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->contactForm->delete()) {
                    errBoatContactFormDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->contactForm)
                    ->setType(ActivityType::BOAT_CONTACT_FORM)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete boat contact form. Name: " . $this->contactForm->name);
            });

            return success();
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function markAsRead()
    {
        try {

            DB::transaction(function () {
                $this->contactForm->update(['isRead' => true]);
            });

            return success($this->contactForm->fresh()->load('boat'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function changeStatus(ChangeMailStatusRequest $request): BoatContactForm
    {
        $this->contactForm->update([
            'statusId' => $request->statusId,
        ]);

        return success($this->contactForm->fresh()->load('boat'));
    }
}
