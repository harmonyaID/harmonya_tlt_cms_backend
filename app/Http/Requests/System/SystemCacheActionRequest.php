<?php

namespace App\Http\Requests\System;

use Logia\Core\Validation\Support\FormRequest;

class SystemCacheActionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'action' => 'required|string|in:cache-clear,config-clear,route-clear,view-clear,optimize,optimize-clear,queue-restart',
        ];
    }
}
