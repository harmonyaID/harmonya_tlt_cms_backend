<?php

namespace App\Http\Controllers\Web\Admin\Team;

use App\Algorithms\Team\TeamMemberAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\Team\TeamMemberRequest;
use App\Models\Team\TeamMember;
use App\Parser\Team\TeamMemberParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_TEAM_MEMBER_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_TEAM_MEMBER_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_TEAM_MEMBER_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_TEAM_MEMBER_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);
        }
    }

    public function get(Request $request)
    {
        $members = TeamMember::filter($request)->getOrPaginate($request);
        return success(TeamMemberParser::briefs($members), pagination: pagination($members));
    }

    public function detail($id)
    {
        $member = TeamMember::find($id);
        if (!$member) {
            errTeamMemberGet();
        }

        return success(TeamMemberParser::first($member));
    }

    public function create(TeamMemberRequest $request)
    {
        $algo = new TeamMemberAlgo();
        return $algo->create($request);
    }

    public function update($id, TeamMemberRequest $request)
    {
        $algo = new TeamMemberAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new TeamMemberAlgo((int)$id);
        return $algo->delete();
    }

    protected function trashModel(): string
    {
        return TeamMember::class;
    }

    protected function trashParser(): string
    {
        return TeamMemberParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::TEAM_MEMBER;
    }

    protected function trashLabel($item): string
    {
        return $item->name;
    }
}
