<?php

namespace App\Http\Requests\Offer;

use App\Http\Requests\Acf\AcfRule;
use App\Http\Requests\Seo\SeoRule;
use Illuminate\Validation\Rule;
use Logia\Core\Validation\Support\FormRequest;

class OfferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $offerId = $this->route('id');

        return array_merge([
            'title' => 'required|string',

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('offers', 'slug')->ignore($offerId),
            ],
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date|after_or_equal:startDate',
            'publishedAt' => 'nullable|date',
            'isActive' => 'required|boolean',
            'locale' => 'nullable|string|exists:languages,code',

            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'deleteThumbnail' => 'nullable|boolean',

            'propertyIds' => 'nullable|array',
            'propertyIds.*' => 'integer|exists:properties,id',

            'seo' => 'nullable|array',
        ], SeoRule::rules(), AcfRule::rules());
    }
}
