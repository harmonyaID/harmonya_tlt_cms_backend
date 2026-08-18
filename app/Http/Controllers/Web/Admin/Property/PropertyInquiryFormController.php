<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyInquiryFormAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeMailStatusRequest;
use App\Http\Requests\Property\PropertyInquiryFormRequest;
use App\Models\Property\PropertyInquiryForm;
use App\Parser\Property\PropertyInquiryFormParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class PropertyInquiryFormController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_INQUIRY_FORM_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_INQUIRY_FORM_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_INQUIRY_FORM_UPDATE);
                return $next($request);
            })->only(['markAsRead', 'changeStatus']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_INQUIRY_FORM_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $forms = PropertyInquiryForm::filter($request)->with('property', 'sourceType')->getOrPaginate($request);
        return success(PropertyInquiryFormParser::briefs($forms), pagination: pagination($forms));
    }

    public function detail($id)
    {
        $form = PropertyInquiryForm::with('property', 'sourceType')->find($id);
        if (!$form) {
            errPropertyInquiryFormGet();
        }

        return success(PropertyInquiryFormParser::first($form));
    }

    public function create(PropertyInquiryFormRequest $request)
    {
        $algo = new PropertyInquiryFormAlgo();
        return $algo->create($request);
    }

    public function delete($id)
    {
        $algo = new PropertyInquiryFormAlgo((int)$id);
        return $algo->delete();
    }

    public function markAsRead($id)
    {
        $algo = new PropertyInquiryFormAlgo((int)$id);
        return $algo->markAsRead();
    }

    public function changeStatus($id, ChangeMailStatusRequest $request)
    {
        $algo = new PropertyInquiryFormAlgo((int)$id);
        return $algo->changeStatus($request);
    }
}
