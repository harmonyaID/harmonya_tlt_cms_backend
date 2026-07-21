<?php

namespace App\Algorithms\WebsiteContactForm;

use App\Http\Requests\ChangeMailStatusRequest;
use App\Http\Requests\Website\WebsiteContactFormChangeStatusRequest;
use App\Mail\WebsiteContactForm\WebsiteContactFormMail;
use App\Models\WebsiteContactForm\WebsiteContactForm;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class WebsiteContactFormAlgo
{
    public function __construct(protected WebsiteContactForm|int|null $contactForm = null)
    {
        if (is_int($this->contactForm)) {
            $this->contactForm = WebsiteContactForm::find($this->contactForm);
            if (!$this->contactForm) {
                errWebsiteContactFormGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->contactForm = WebsiteContactForm::create($request->all());
                if (!$this->contactForm) {
                    errWebsiteContactFormSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->contactForm)
                    ->setType(ActivityType::WEBSITE_CONTACT_FORM)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new contact form. Name: " . $this->contactForm->name);
            });

            if (config('mail.notification_to')) {
                try {
                    Mail::to(config('mail.notification_to'))
                        ->queue(new WebsiteContactFormMail($this->contactForm));
                } catch (\Throwable $e) {
                    logger()->error('Failed to send WebsiteContactFormMail: ' . $e->getMessage());
                }
            }

            return success($this->contactForm);
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->contactForm->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->contactForm)
                    ->setType(ActivityType::WEBSITE_CONTACT_FORM)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update contact form. Name: " . $this->contactForm->name);
            });

            return success($this->contactForm);
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->contactForm->delete()) {
                    errWebsiteContactFormDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->contactForm)
                    ->setType(ActivityType::WEBSITE_CONTACT_FORM)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete contact form. Name: " . $this->contactForm->name);
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

            return success($this->contactForm->fresh());
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function changeStatus(ChangeMailStatusRequest $request) {
        $this->contactForm->update([
            'statusId' => $request->statusId,
        ]);

        return success($this->contactForm->fresh());

    }
}