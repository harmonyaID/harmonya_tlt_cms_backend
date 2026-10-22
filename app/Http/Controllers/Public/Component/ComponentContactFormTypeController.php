<?php

namespace App\Http\Controllers\Public\Component;

use App\Http\Controllers\Controller;
use App\Models\Component\ComponentContactFormInquiryType;
use App\Models\Component\ComponentContactFormType;

class ComponentContactFormTypeController extends Controller
{
    /**
     * Type of Contact (options for the contact form).
     */
    public function types()
    {
        return success($this->options(ComponentContactFormType::orderBy('id')->get()));
    }

    /**
     * Type of Inquiry (options for the contact form).
     */
    public function inquiryTypes()
    {
        return success($this->options(ComponentContactFormInquiryType::orderBy('id')->get()));
    }

    private function options($items): array
    {
        return $items->map(fn ($item) => [
            'id' => $item->id,
            'name' => $item->name,
            'eventId' => $item->eventId,
        ])->values()->all();
    }
}
