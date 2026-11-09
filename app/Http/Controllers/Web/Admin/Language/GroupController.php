<?php

namespace App\Http\Controllers\Web\Admin\Language;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StaffRequest;
use App\Models\Language\LanguageGroup;
use App\Parser\Staff\StaffParser;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function get(Request $request)
    {
        $groups = LanguageGroup::filter($request)->get();
        return success(StaffParser::briefs($groups));
    }

    public function update($id, StaffRequest $request)
    {
        $algo = new ((int)$id);
        return $algo->update($request);
    }
}
