<?php

namespace App\Algorithms\Property;

use App\Http\Requests\ChangeMailStatusRequest;
use App\Mail\Property\PropertyContactFormMail;
use App\Models\Property\PropertyContactForm;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PropertyContactFormAlgo
{
    /**
     * @param PropertyContactForm|int|null $contactForm
     */
    public function __construct(protected PropertyContactForm|int|null $contactForm = null)
    {
        if (is_int($this->contactForm)) {
            $this->contactForm = PropertyContactForm::find($this->contactForm);
            if (!$this->contactForm) {
                errPropertyContactFormGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->contactForm = PropertyContactForm::create($request->all());
                if (!$this->contactForm) {
                    errPropertyContactFormSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->contactForm)
                    ->setType(ActivityType::PROPERTY_CONTACT_FORM)
                    ->setAction(ActivityAction::CREATE)
                    ->log("New property booking form submitted. Name: " . $this->contactForm->name);
            });

            if (config('mail.notification_to')) {
                try {
                    Mail::to(config('mail.notification_to'))
                        ->queue(new PropertyContactFormMail($this->contactForm));
                } catch (\Throwable $e) {
                    logger()->error('Failed to send PropertyContactFormMail: ' . $e->getMessage());
                }
            }

            return success($this->contactForm->fresh()->load('property'));

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->contactForm->delete()) {
                    errPropertyContactFormDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->contactForm)
                    ->setType(ActivityType::PROPERTY_CONTACT_FORM)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete property contact form. Name: " . $this->contactForm->name);
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

            return success($this->contactForm->fresh()->load('property'));

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function changeStatus(ChangeMailStatusRequest $request)
    {
        $this->contactForm->update([
            'statusId' => $request->statusId,
        ]);

        return success($this->contactForm->fresh()->load('property'));
    }
}
