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

            // VIEW
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_ACCESS_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            // CREATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_STAFF_CREATE);
                return $next($request);
            })->only(['create']);

            // UPDATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_STAFF_UPDATE);
                return $next($request);
            })->only(['update', 'activation', 'changePassword']);

            // DELETE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_STAFF_DELETE);
                return $next($request);
            })->only(['delete']);

            // ADMIN
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_STAFF);
                return $next($request);
            })->only(['updateSuperadmin']);

        }
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $staffs = Staff::filter($request)->getOrPaginate($request);
        return success(StaffParser::briefs($staffs), pagination: pagination($staffs));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function detail($id)
    {
        $staff = Staff::find($id);
        if (!$staff) {
            errStaffGet();
        }

        return success($staff);
    }

    /**
     * @param StaffRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function create(StaffRequest $request)
    {
        $algo = new StaffAlgo();
        return $algo->create($request);
    }

    /**
     * @param $id
     * @param StaffRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update($id, StaffRequest $request)
    {
        $algo = new StaffAlgo((int)$id);
        return $algo->update($request);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function delete($id)
    {
        $algo = new StaffAlgo((int)$id);
        return $algo->delete();
    }

    /**
     * @param $id
     * @param ActivationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function activation($id, ActivationRequest $request)
    {
        $algo = new StaffAlgo((int)$id);
        return $algo->activation($request);
    }

    /**
     * @param $id
     * @param ResetPasswordRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function changePassword($id, ResetPasswordRequest $request)
    {
        $algo = new StaffAlgo((int)$id);
        return $algo->changePassword($request);
    }
}
