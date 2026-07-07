<?php

namespace App\Http\Requests\Blog;

use Logia\Core\Validation\Support\FormRequest;

class BlogRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'categoryId' => 'nullable|integer|exists:blog_categories,id',
            'title' => 'required|string',
            'slug' => 'required|string',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'author' => 'nullable|string',
            'publishedAt' => 'nullable|date',
            'isActive' => 'required|boolean',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tagIds' => 'nullable|array',
            'tagIds.*' => 'integer|exists:blog_tags,id',
        ];
    }
}