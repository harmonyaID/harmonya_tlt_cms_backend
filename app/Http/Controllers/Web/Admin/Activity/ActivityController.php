<?php

namespace App\Http\Controllers\Web\Admin\Activity;

use App\Http\Controllers\Controller;
use App\Models\Activity\Activity;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $activities = Activity::filter($request)->orderBy('createdAt', 'DESC')->getOrPaginate($request, true);
        return success($activities);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getType()
    {
        return success(ActivityType::get());
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getAction()
    {
        return success(ActivityAction::get());
    }

}
