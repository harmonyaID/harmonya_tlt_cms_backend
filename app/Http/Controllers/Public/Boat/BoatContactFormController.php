<?php

namespace App\Http\Controllers\Public\Boat;

use App\Algorithms\Boat\BoatContactFormAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Boat\BoatContactFormRequest;

class BoatContactFormController extends Controller
{
    public function create(BoatContactFormRequest $request)
    {
        $algo = new BoatContactFormAlgo();
        return $algo->create($request);
    }
}
