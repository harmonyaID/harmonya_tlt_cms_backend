<?php

namespace App\Http\Requests\Blog;

use App\Http\Requests\Acf\AcfRule;
use App\Http\Requests\Seo\SeoRequest;
use App\Http\Requests\Seo\SeoRule;
use Illuminate\Validation\Rule;
use Logia\Core\Validation\Support\FormRequest;

class BlogRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $blogId = $this->route('id');

        return array_merge([
            'categoryId' => 'nullable|integer|exists:blog_categories,id',
            'title' => 'required|string',

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blogs', 'slug')->ignore($blogId),
            ],
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'author' => 'nullable|string',
            'publishedAt' => 'nullable|date',
            'isActive' => 'required|boolean',
            'locale' => 'nullable|string|exists:languages,code',

            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deleteThumbnail' => 'nullable|boolean',

            'promoBanner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'promoBannerUrl' => 'nullable|url|max:2048',
            'deletePromoBanner' => 'nullable|boolean',

            'propertyIds' => 'nullable|array|max:9',
            'propertyIds.*' => 'integer|distinct|exists:properties,id',

            'tagIds' => 'nullable|array',
            'tagIds.*' => 'integer|exists:blog_tags,id',

            'seo' => 'nullable|array',
        ], SeoRule::rules(), AcfRule::rules());
    }
}
