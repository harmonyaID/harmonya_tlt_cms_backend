<?php

namespace App\Http\Requests\Setting;

use App\Services\Constant\Setting\NotificationProvider;
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
        ];
    }
}