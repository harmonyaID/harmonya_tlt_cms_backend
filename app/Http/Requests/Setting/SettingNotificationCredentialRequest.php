<?php

namespace App\Http\Requests\Setting;

use App\Services\Constant\Setting\MailtrapCredential;
use App\Services\Constant\Setting\NotificationProvider;
use App\Services\Constant\Setting\PostmarkCredential;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingNotificationCredentialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'providerId' => [
                'required',
                'integer',
                Rule::in(array_keys(NotificationProvider::OPTION)),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'credentials' => [
                'required',
                'array',
            ],

            'isActive' => [
                'required',
                'boolean',
            ],
        ] + $this->mailCredentialRules();
    }

    private function mailCredentialRules(): array
    {
        $postmark = 'required_if:providerId,' . NotificationProvider::POSTMARK_ID;
        $mailtrap = 'required_if:providerId,' . NotificationProvider::MAILTRAP_ID;

        return [
            'credentials.' . PostmarkCredential::SERVER_TOKEN => [$postmark, 'nullable', 'string'],
            'credentials.' . MailtrapCredential::USERNAME => [$mailtrap, 'nullable', 'string'],
            'credentials.' . MailtrapCredential::PASSWORD => [$mailtrap, 'nullable', 'string'],
            'credentials.' . MailtrapCredential::HOST => ['nullable', 'string', 'max:255'],
            'credentials.' . MailtrapCredential::PORT => ['nullable', 'integer', 'between:1,65535'],

            // fromEmail / fromName are shared by Postmark and Mailtrap
            'credentials.' . PostmarkCredential::FROM_EMAIL => [$postmark, $mailtrap, 'nullable', 'email'],
            'credentials.' . PostmarkCredential::FROM_NAME => ['nullable', 'string', 'max:255'],

            // admin recipient(s) of the form notifications: "a@x.com, b@x.com"
            'credentials.' . PostmarkCredential::TO_EMAIL => ['nullable', 'string', 'max:1000', function ($attribute, $value, $fail) {
                foreach (array_filter(array_map('trim', explode(',', (string) $value))) as $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $fail("\"{$email}\" is not a valid email address.");
                    }
                }
            }],
        ];
    }
}