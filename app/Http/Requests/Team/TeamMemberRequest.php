<?php

namespace App\Http\Requests\Team;

use Logia\Core\Validation\Support\FormRequest;

class TeamMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'role' => 'nullable|string',
            'question' => 'nullable|string',
            'answer' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'isActive' => 'required|boolean',

            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deletePhoto' => 'nullable|boolean',
        ];
    }
}
