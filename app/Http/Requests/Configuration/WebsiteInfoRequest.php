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

        $emails = $this->input('emails');

        $emailsRules = [];
        if ($emails && is_array($emails)) {
            if (count($emails) > 0) {
                $emailsRules = [
                    'emails.*.title' => 'required|string',
                    'emails.*.email' => 'required|email',
                ];
            }
        }

        $phones = $this->input('phones');

        $phonesRules = [];
        if ($phones && is_array($phones)) {
            if (count($phones) > 0) {
                $phonesRules = [
                    'phones.*.title' => 'required|string',
                    'phones.*.phone' => 'required|string',
                ];
            }
        }

        return [
                'title' => 'required|string',
                'emails' => 'array|nullable',
                'phones' => 'array|nullable',
                'fax' => 'nullable',
                'whatsapp' => 'nullable',
                'country' => 'required|string',
                'postalCode' => 'required',
                'address' => 'required|string',
                'mapEmbed' => 'string|nullable',
                'socialMedia' => 'array|nullable'
            ] + $emailsRules + $phonesRules + $socialMediaRules;
    }
}
