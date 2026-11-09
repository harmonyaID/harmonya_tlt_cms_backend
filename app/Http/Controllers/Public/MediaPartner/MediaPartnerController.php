<?php

namespace App\Http\Controllers\Public\MediaPartner;

use App\Http\Controllers\Controller;
use App\Models\MediaPartner\MediaPartner;
use App\Parser\MediaPartner\MediaPartnerParser;
use Illuminate\Http\Request;

class MediaPartnerController extends Controller
{
    public function get(Request $request)
    {
        $partners = MediaPartner::filter($request)->where('isPublish', true)->getOrPaginate($request);
        return success(MediaPartnerParser::briefs($partners), pagination: pagination($partners));
    }
}
