<?php

namespace App\Http\Requests\Blog;

use Logia\Core\Validation\Support\FormRequest;

class BlogAffiliateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * multipart/form-data (sent because of the banner upload) turns true / false
     * into the strings "true" / "false", which the boolean rule rejects.
     */
    protected function prepareForValidation()
    {
        foreach (['isAppliedToAll', 'deleteBannerImage'] as $field) {
            $value = $this->input($field);

            if (is_string($value) && ($bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) !== null) {
                $this->merge([$field => $bool]);
            }
        }
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'bannerImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'deleteBannerImage' => 'nullable|boolean',
            'bannerUrl' => 'nullable|url|max:2048',
            'isAppliedToAll' => 'nullable|boolean',
        ];
    }
}
