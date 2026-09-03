<?php

namespace App\Http\Requests\System;

use Logia\Core\Validation\Support\FormRequest;

class SystemCacheActionRequest extends FormRequest
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
        return [
            'action' => 'required|string|in:cache-clear,config-clear,route-clear,view-clear,optimize,optimize-clear,queue-restart',
        ];
    }
}
