<?php

namespace App\Http\Controllers\Public\Team;

use App\Http\Controllers\Controller;
use App\Models\Team\TeamMember;
use App\Parser\Team\TeamMemberParser;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function get(Request $request)
    {
        $request->merge(['isActive' => true]);

        $members = TeamMember::filter($request)->getOrPaginate($request);
        return success(TeamMemberParser::briefs($members), pagination: pagination($members));
    }

    /**
     * Distinct list of roles currently in use, e.g. for building a filter
     * dropdown on the public "Our Team" page ("Founder", "Marketing Director", ...).
     */
    public function getRoles()
    {
        $roles = TeamMember::where('isActive', true)
            ->whereNotNull('role')
            ->distinct()
            ->orderBy('role')
            ->pluck('role');

        return success($roles);
    }
}
