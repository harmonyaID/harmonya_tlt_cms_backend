<?php

namespace App\Http\Requests\Setting;

use Logia\Core\Validation\Support\FormRequest;

class SettingAmenityCategoryIconRequest extends FormRequest
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
     * Send as multipart/form-data. Either upload a new `icon` image,
     * or send `deleteIcon=1` to remove the current one.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'icon' => 'required_without:deleteIcon|file|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'deleteIcon' => 'nullable|boolean',
        ];
    }
}
