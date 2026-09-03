<?php

namespace App\Http\Controllers\Web\Admin\TltReview;

use App\Algorithms\TltReview\TltReviewAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\TltReview\TltReviewRequest;
use App\Models\TltReview\TltReview;
use App\Parser\TltReview\TltReviewParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class TltReviewController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            // VIEW
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_TLT_REVIEW_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            // CREATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_TLT_REVIEW_CREATE);
                return $next($request);
            })->only(['create']);

            // UPDATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_TLT_REVIEW_UPDATE);
                return $next($request);
            })->only(['update']);

            // DELETE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_TLT_REVIEW_DELETE);
                return $next($request);
            })->only(['delete']);

        }
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $reviews = TltReview::filter($request)->with('photos')->getOrPaginate($request);
        return success(TltReviewParser::briefs($reviews), pagination: pagination($reviews));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function detail($id)
    {
        $review = TltReview::with('photos')->find($id);
        if (!$review) {
            errTltReviewGet();
        }

        return success(TltReviewParser::first($review));
    }

    /**
     * @param TltReviewRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function create(TltReviewRequest $request)
    {
        $algo = new TltReviewAlgo();
        return $algo->create($request);
    }

    /**
     * @param $id
     * @param TltReviewRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update($id, TltReviewRequest $request)
    {
        $algo = new TltReviewAlgo((int)$id);
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
        $algo = new TltReviewAlgo((int)$id);
        return $algo->delete();
    }
}