<?php

namespace App\Algorithms\WebsiteContactForm;

use App\Models\WebsiteContactForm\WebsiteContactForm;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebsiteContactFormAlgo
{
    /**
     * @param WebsiteContactForm|int|null $contactForm
     */
    public function __construct(protected WebsiteContactForm|int|null $contactForm = null)
    {
        if (is_int($this->contactForm)) {
            $this->contactForm = WebsiteContactForm::find($this->contactForm);
            if (!$this->contactForm) {
                errWebsiteContactFormGet();
            }
        }
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
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

            return success($this->contactForm);

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
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

    /**
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
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

    /**
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
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
}