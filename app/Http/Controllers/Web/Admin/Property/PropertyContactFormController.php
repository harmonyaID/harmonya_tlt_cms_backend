<?php

namespace App\Http\Controllers\Web\Admin\Property;

use App\Algorithms\Property\PropertyContactFormAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeMailStatusRequest;
use App\Http\Requests\Property\PropertyContactFormRequest;
use App\Models\Property\PropertyContactForm;
use App\Parser\Property\PropertyContactFormParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class PropertyContactFormController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_CONTACT_FORM_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_CONTACT_FORM_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_CONTACT_FORM_UPDATE);
                return $next($request);
            })->only(['markAsRead', 'changeStatus']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_PROPERTY_CONTACT_FORM_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $forms = PropertyContactForm::filter($request)->with('property')->getOrPaginate($request);
        return success(PropertyContactFormParser::briefs($forms), pagination: pagination($forms));
    }

    public function detail($id)
    {
        $form = PropertyContactForm::with('property')->find($id);
        if (!$form) {
            errPropertyContactFormGet();
        }

        return success(PropertyContactFormParser::first($form));
    }

    public function create(PropertyContactFormRequest $request)
    {
        $algo = new PropertyContactFormAlgo();
        return $algo->create($request);
    }

    public function delete($id)
    {
        $algo = new PropertyContactFormAlgo((int)$id);
        return $algo->delete();
    }

    public function markAsRead($id)
    {
        $algo = new PropertyContactFormAlgo((int)$id);
        return $algo->markAsRead();
    }

    public function changeStatus($id, ChangeMailStatusRequest $request)
    {
        $algo = new PropertyContactFormAlgo((int)$id);
        return $algo->changeStatus($request);
    }
}
