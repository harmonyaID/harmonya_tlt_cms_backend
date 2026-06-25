<?php

namespace App\Http\Controllers\Web\Admin\Language;

use App\Algorithms\Staff\StaffAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActivationRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Staff\StaffRequest;
use App\Models\Language\LanguageGroup;
use App\Models\Staff\Staff;
use App\Parser\Staff\StaffParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $groups = LanguageGroup::filter($request)->get();
        return success(StaffParser::briefs($groups));
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
        $algo = new ((int)$id);
        return $algo->update($request);
    }
}
