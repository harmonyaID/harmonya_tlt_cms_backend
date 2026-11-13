
<?php

namespace App\Http\Requests\MediaPartner;

use Logia\Core\Validation\Support\FormRequest;

class MediaPartnerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'title' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
            'additionalContent' => 'nullable|string',
            'url' => 'nullable|string|url',

            'featuredImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'isPublish' => 'required|boolean',
            'isPublishHomepage' => 'required|boolean',

            'typeId' => 'required|integer',
        ];
    }
}
