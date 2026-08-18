<?php

namespace App\Algorithms\Property;

use App\Http\Requests\ChangeMailStatusRequest;
use App\Mail\Property\PropertyInquiryFormMail;
use App\Models\Property\PropertyInquiryForm;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PropertyInquiryFormAlgo
{
    public function __construct(protected PropertyInquiryForm|int|null $inquiryForm = null)
    {
        if (is_int($this->inquiryForm)) {
            $this->inquiryForm = PropertyInquiryForm::find($this->inquiryForm);
            if (!$this->inquiryForm) {
                errPropertyInquiryFormGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->inquiryForm = PropertyInquiryForm::create($request->all());
                if (!$this->inquiryForm) {
                    errPropertyInquiryFormSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->inquiryForm)
                    ->setType(ActivityType::PROPERTY_INQUIRY_FORM)
                    ->setAction(ActivityAction::CREATE)
                    ->log("New property inquiry submitted. Name: " . $this->inquiryForm->name);
            });

            if (config('mail.notification_to')) {
                try {
                    Mail::to(config('mail.notification_to'))
                        ->queue(new PropertyInquiryFormMail($this->inquiryForm));
                } catch (\Throwable $e) {
                    logger()->error('Failed to send PropertyInquiryFormMail: ' . $e->getMessage());
                }
            }

            return success(
                $this->inquiryForm->fresh()->load('property', 'sourceType'),
                'Thank you for your inquiry. Our team will get back to you within 24 hours.'
            );

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->inquiryForm->delete()) {
                    errPropertyInquiryFormDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->inquiryForm)
                    ->setType(ActivityType::PROPERTY_INQUIRY_FORM)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete property inquiry form. Name: " . $this->inquiryForm->name);
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
                $this->inquiryForm->update(['isRead' => true]);
            });

            return success($this->inquiryForm->fresh()->load('property', 'sourceType'));

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function changeStatus(ChangeMailStatusRequest $request)
    {
        $this->inquiryForm->update([
            'statusId' => $request->statusId,
        ]);

        return success($this->inquiryForm->fresh()->load('property', 'sourceType'));
    }
}
