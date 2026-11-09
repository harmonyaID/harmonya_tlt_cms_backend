<?php

namespace App\Http\Controllers\Web\Admin\Staff;

use App\Algorithms\Staff\StaffAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActivationRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Staff\StaffRequest;
use App\Models\Staff\Staff;
use App\Parser\Staff\StaffParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ACCESS_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_STAFF_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_STAFF_UPDATE);
                return $next($request);
            })->only(['update', 'activation', 'changePassword']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_STAFF_DELETE);
                return $next($request);
            })->only(['delete']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_STAFF);
                return $next($request);
            })->only(['updateSuperadmin']);
        }
    }

    public function get(Request $request)
    {
        $staffs = Staff::filter($request)->getOrPaginate($request);
        return success(StaffParser::briefs($staffs), pagination: pagination($staffs));
    }

    public function detail($id)
    {
        $staff = Staff::find($id);
        if (!$staff) errStaffGet();

        return success(StaffParser::first($staff));
    }

    public function create(StaffRequest $request)
    {
        $algo = new StaffAlgo();
        return $algo->create($request);
    }

    public function update($id, StaffRequest $request)
    {
        $algo = new StaffAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new StaffAlgo((int)$id);
        return $algo->delete();
    }

    public function activation($id, ActivationRequest $request)
    {
        $algo = new StaffAlgo((int)$id);
        return $algo->activation($request);
    }

    public function changePassword($id, ResetPasswordRequest $request)
    {
        $algo = new StaffAlgo((int)$id);
        return $algo->changePassword($request);
    }
}
