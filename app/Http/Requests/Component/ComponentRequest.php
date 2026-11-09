<?php

namespace App\Http\Requests\Component;

use Logia\Core\Validation\Support\FormRequest;

class ComponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'eventId' => 'string|nullable'
        ];
    }
}
