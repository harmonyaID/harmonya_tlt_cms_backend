<?php

namespace App\Http\Requests\Setting;

use Logia\Core\Validation\Support\FormRequest;

class SettingPropertyFeatureRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * multipart/form-data (sent because of the icon upload) turns true / false into the
     * strings "true" / "false", which the boolean rule rejects.
     */
    protected function prepareForValidation()
    {
        foreach (['hasValue', 'deleteIcon'] as $field) {
            $value = $this->input($field);

            if (is_string($value) && ($bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) !== null) {
                $this->merge([$field => $bool]);
            }
        }
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'icon' => 'nullable|file|mimes:jpg,jpeg,png,svg,webp|max:2048',
            'hasValue' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'deleteIcon' => 'nullable|boolean',
        ];
    }
}
