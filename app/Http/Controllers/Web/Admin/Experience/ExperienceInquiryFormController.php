<?php

namespace App\Http\Controllers\Web\Admin\Experience;

use App\Algorithms\Experience\ExperienceInquiryFormAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeMailStatusRequest;
use App\Http\Requests\Experience\ExperienceInquiryFormRequest;
use App\Models\Experience\ExperienceInquiryForm;
use App\Parser\Experience\ExperienceInquiryFormParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class ExperienceInquiryFormController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            // VIEW
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_INQUIRY_FORM_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            // CREATE — dipakai FE publik, bisa tanpa permission
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_INQUIRY_FORM_CREATE);
                return $next($request);
            })->only(['create']);

            // DELETE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_EXPERIENCE_INQUIRY_FORM_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $forms = ExperienceInquiryForm::filter($request)->with('experience')->getOrPaginate($request);
        return success(ExperienceInquiryFormParser::briefs($forms), pagination: pagination($forms));
    }

    public function detail($id)
    {
        $form = ExperienceInquiryForm::with('experience')->find($id);
        if (!$form) errExperienceInquiryFormGet();

        return success(ExperienceInquiryFormParser::first($form));
    }

    public function create(ExperienceInquiryFormRequest $request)
    {
        return (new ExperienceInquiryFormAlgo())->create($request);
    }

    public function delete($id)
    {
        return (new ExperienceInquiryFormAlgo((int)$id))->delete();
    }

    public function changeStatus($id, ChangeMailStatusRequest $request)
    {
        $algo = new ExperienceInquiryFormAlgo((int)$id);
        return $algo->changeStatus($request);
    }
}
