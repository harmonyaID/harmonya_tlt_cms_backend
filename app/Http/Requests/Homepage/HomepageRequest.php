<?php

namespace App\Http\Requests\Homepage;

use App\Http\Requests\Seo\SeoRule;
use App\Models\Homepage\Homepage;
use Logia\Core\Validation\Support\FormRequest;

class HomepageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $homepageId = $this->route('id');
        $homepage = Homepage::with('seo')->find($homepageId);

        return array_merge([
            'value' => 'required|array',
            'locale' => 'nullable|string|max:10',

            'seo' => 'nullable|array',
        ], SeoRule::rules('seo.', $homepage?->seo));
    }
}
