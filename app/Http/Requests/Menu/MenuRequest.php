<?php

namespace App\Http\Requests\Menu;

use Logia\Core\Validation\Support\FormRequest;

class MenuRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'title' => 'required|string',
            'handle' => 'nullable|string',
            'groupId' => 'nullable|integer',
            'locale' => 'required|string',

            'items' => 'nullable|array',
            'items.*.menuLabel' => 'required_with:items|string',
            'items.*.menuUrl' => 'required_with:items|string',
            'items.*.menuOrder' => 'nullable|integer',
            'items.*.menuParent' => 'nullable|integer',
            'items.*.typeId' => 'nullable|integer|in:1,2',
            'items.*.description' => 'nullable|string',
            'items.*.featuredImage' => $this->featuredImageRules(),

            'items.*.children' => 'nullable|array',
            'items.*.children.*.menuLabel' => 'required_with:items.*.children|string',
            'items.*.children.*.menuUrl' => 'required_with:items.*.children|string',
            'items.*.children.*.menuOrder' => 'nullable|integer',
            'items.*.children.*.typeId' => 'nullable|integer|in:1,2',
            'items.*.children.*.description' => 'nullable|string',
            'items.*.children.*.featuredImage' => $this->featuredImageRules(),
        ];
    }

    /**
     * featuredImage boleh berupa file baru (upload) atau string URL gambar lama
     * (hasil GET) yang dikirim balik agar gambar tetap dipertahankan.
     */
    private function featuredImageRules(): array
    {
        return [
            'nullable',
            function ($attribute, $value, $fail) {
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $validator = validator(
                        ['file' => $value],
                        ['file' => 'image|mimes:jpg,jpeg,png,webp|max:2048']
                    );
                    if ($validator->fails()) {
                        $fail($validator->errors()->first('file'));
                    }
                    return;
                }

                if (!is_string($value)) {
                    $fail('The ' . $attribute . ' must be an image file or an image URL.');
                }
            },
        ];
    }
}
