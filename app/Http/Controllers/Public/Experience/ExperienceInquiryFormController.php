<?php

namespace App\Http\Controllers\Public\Experience;

use App\Algorithms\Experience\ExperienceInquiryFormAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Experience\ExperienceInquiryFormRequest;

class ExperienceInquiryFormController extends Controller
{
    public function create(ExperienceInquiryFormRequest $request)
    {
        $algo = new ExperienceInquiryFormAlgo();
        return $algo->create($request);
    }
}
