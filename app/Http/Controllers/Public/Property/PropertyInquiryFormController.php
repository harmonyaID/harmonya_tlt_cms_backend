<?php

namespace App\Http\Controllers\Public\Property;

use App\Algorithms\Property\PropertyInquiryFormAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Property\PropertyInquiryFormRequest;

class PropertyInquiryFormController extends Controller
{
    public function create(PropertyInquiryFormRequest $request)
    {
        $algo = new PropertyInquiryFormAlgo();
        return $algo->create($request);
    }
}
