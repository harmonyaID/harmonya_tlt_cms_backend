<?php

namespace App\Http\Requests\Page;

use App\Http\Requests\Acf\AcfRule;
use App\Http\Requests\Seo\SeoRule;
use Logia\Core\Validation\Support\FormRequest;

class PageRequest extends FormRequest
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
        return array_merge([
            'title' => 'required|string|max:256',
            'shortDescription' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|string',
            'template' => 'nullable|string',
            'groupId' => 'nullable|integer',
            'locale' => 'nullable|string|exists:languages,code',

            'featuredImage' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deleteFeaturedImage' => 'nullable|boolean',

            'seo' => 'nullable|array',
        ], SeoRule::rules(), AcfRule::rules());
    }
}
