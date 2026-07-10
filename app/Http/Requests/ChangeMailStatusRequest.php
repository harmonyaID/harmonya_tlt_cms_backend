<?php

namespace App\Http\Requests;

use App\Services\Constant\Global\MailStatus;
use Illuminate\Foundation\Http\FormRequest;

class ChangeMailStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'statusId' => [
                'required',
                'integer',
                'in:' . implode(',', array_keys(MailStatus::OPTION)),
            ],
        ];
    }
}