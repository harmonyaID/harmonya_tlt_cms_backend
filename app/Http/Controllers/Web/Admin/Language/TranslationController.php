<?php

namespace App\Http\Controllers\Web\Admin\Language;

use App\Algorithms\Staff\StaffAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StaffRequest;
use App\Models\Staff\Staff;
use App\Parser\Staff\StaffParser;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function get(Request $request)
    {
        $staffs = Staff::filter($request)->getOrPaginate($request);
        return success(StaffParser::briefs($staffs), pagination: pagination($staffs));
    }

    public function update($id, StaffRequest $request)
    {
        $algo = new StaffAlgo((int)$id);
        return $algo->update($request);
    }

    public function updateSpecificLang($id, $locale, Request $request)
    {
        $algo = new StaffAlgo((int)$id);
        return $algo->update($request);
    }
}
