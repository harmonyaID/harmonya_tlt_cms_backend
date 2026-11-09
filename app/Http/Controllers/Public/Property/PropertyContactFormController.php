<?php

namespace App\Http\Controllers\Public\Property;

use App\Algorithms\Property\PropertyContactFormAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Property\PropertyContactFormRequest;

class PropertyContactFormController extends Controller
{
    public function create(PropertyContactFormRequest $request)
    {
        $algo = new PropertyContactFormAlgo();
        return $algo->create($request);
    }
}
