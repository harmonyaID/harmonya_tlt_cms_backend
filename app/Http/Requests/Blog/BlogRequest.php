<?php

namespace App\Http\Requests\Blog;

use App\Http\Requests\Acf\AcfRule;
use App\Http\Requests\Seo\SeoRequest;
use App\Http\Requests\Seo\SeoRule;
use App\Models\Blog\Blog;
use Illuminate\Validation\Rule;
use Logia\Core\Validation\Support\FormRequest;

class BlogRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /** affiliateIds may come from form-data as text: "1,2" -> [1, 2]. */
    protected function prepareForValidation()
    {
        $value = $this->input('affiliateIds');

        if (is_string($value) && trim($value) !== '') {
            $this->merge(['affiliateIds' => array_values(array_filter(array_map('trim', explode(',', $value)), 'strlen'))]);
        }
    }

    public function rules()
    {
        $blogId = $this->route('id');

        $blog = Blog::with('seo')->find($blogId);

        return array_merge([
            'categoryId' => 'nullable|integer|exists:blog_categories,id',
            'categoryIds' => 'nullable|array',
            'categoryIds.*' => 'integer|exists:blog_categories,id',

            'title' => 'required|string',

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blogs', 'slug')->ignore($blogId),
            ],
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'additionalContent' => 'nullable|string',
            'author' => 'nullable|string',
            'publishedAt' => 'nullable|date',
            'visibility' => 'nullable|boolean',
            'isActive' => 'required|boolean',
            'locale' => 'nullable|string|exists:languages,code',

            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deleteThumbnail' => 'nullable|boolean',

            // replaces promoBanner: only affiliates that are not applied to every blog can be assigned
            'affiliateIds' => 'nullable|array',
            'affiliateIds.*' => [
                'integer',
                'distinct',
                Rule::exists('blog_affiliates', 'id')->where('isAppliedToAll', false)->whereNull('deletedAt'),
            ],

            'propertyIds' => 'nullable|array|max:9',
            'propertyIds.*' => 'integer|distinct|exists:properties,id',

            'tagIds' => 'nullable|array',
            'tagIds.*' => 'integer|exists:blog_tags,id',

            'seo' => 'nullable|array',
        ], SeoRule::rules('seo.', $blog?->seo), AcfRule::rules());
    }
}
