<?php

namespace App\Http\Requests\Blog;

use Logia\Core\Validation\Support\FormRequest;

class BlogCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
        ];
    }
}