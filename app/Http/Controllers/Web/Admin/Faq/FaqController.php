<?php

namespace App\Http\Controllers\Web\Admin\Faq;

use App\Algorithms\Faq\FaqAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Faq\FaqRequest;
use App\Models\Faq\Faq;
use App\Parser\Faq\FaqParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_FAQ_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_FAQ_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_FAQ_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_FAQ_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $faqs = Faq::filter($request)->getOrPaginate($request);
        return success(FaqParser::briefs($faqs), pagination: pagination($faqs));
    }

    public function detail($id)
    {
        $faq = Faq::find($id);
        if (!$faq) errFaqGet();

        return success(FaqParser::first($faq));
    }

    public function create(FaqRequest $request)
    {
        $algo = new FaqAlgo();
        return $algo->create($request);
    }

    public function update($id, FaqRequest $request)
    {
        $algo = new FaqAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new FaqAlgo((int)$id);
        return $algo->delete();
    }
}