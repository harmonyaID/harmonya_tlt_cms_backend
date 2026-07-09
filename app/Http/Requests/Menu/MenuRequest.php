<?php

namespace App\Http\Requests\Menu;

use Logia\Core\Validation\Support\FormRequest;

class MenuRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'title' => 'required|string',
            'handle' => 'nullable|string',
            'groupId' => 'nullable|integer',
            'locale' => 'required|string',

            'items' => 'nullable|array',
            'items.*.menuLabel' => 'required_with:items|string',
            'items.*.menuUrl' => 'required_with:items|string',
            'items.*.menuOrder' => 'nullable|integer',
            'items.*.menuParent' => 'nullable|integer',
            'items.*.children' => 'nullable|array',
            'items.*.children.*.menuLabel' => 'required_with:items.*.children|string',
            'items.*.children.*.menuUrl' => 'required_with:items.*.children|string',
            'items.*.children.*.menuOrder' => 'nullable|integer',
        ];
    }
}