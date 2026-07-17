<?php

namespace App\Http\Controllers\Public\WebsiteContactForm;

use App\Algorithms\WebsiteContactForm\WebsiteContactFormAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\WebsiteContactForm\WebsiteContactFormRequest;

class WebsiteContactFormController extends Controller
{
    public function create(WebsiteContactFormRequest $request)
    {
        $algo = new WebsiteContactFormAlgo();
        return $algo->create($request);
    }
}
