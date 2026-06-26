<?php

namespace App\Http\Requests\Configuration;

use Logia\Core\Validation\Support\FormRequest;

class WebsiteInfoRequest extends FormRequest
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
        $socialMedia = $this->input('socialMedia');

        $socialMediaRules = [];
        if ($socialMedia && is_array($socialMedia)) {
            if (count($socialMedia) > 0) {
                $socialMediaRules = [
                    'socialMedia.*.name' => 'required|string',
                    'socialMedia.*.icon' => 'required|string',
                    'socialMedia.*.link' => 'required|string',
                ];
            }
        }

        return [
                'title' => 'required|string',
                'email' => 'required|string',
                'phone' => 'nullable',
                'fax' => 'nullable',
                'whatsapp' => 'nullable',
                'country' => 'required|string',
                'postalCode' => 'required',
                'address' => 'required|string',
                'mapEmbed' => 'string|nullable',
                'socialMedia' => 'array|nullable'
            ] + $socialMediaRules;
    }
}
