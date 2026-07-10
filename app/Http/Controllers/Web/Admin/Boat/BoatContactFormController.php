<?php

namespace App\Http\Controllers\Web\Admin\Boat;

use App\Algorithms\Boat\BoatContactFormAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Boat\BoatContactFormRequest;
use App\Http\Requests\ChangeMailStatusRequest;
use App\Models\Boat\BoatContactForm;
use App\Parser\Boat\BoatContactFormParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class BoatContactFormController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_CONTACT_FORM_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_CONTACT_FORM_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_CONTACT_FORM_UPDATE);
                return $next($request);
            })->only(['markAsRead']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_BOAT_CONTACT_FORM_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $forms = BoatContactForm::filter($request)->with('boat', 'boatType')->getOrPaginate($request);
        return success(BoatContactFormParser::briefs($forms), pagination: pagination($forms));
    }

    public function detail($id)
    {
        $form = BoatContactForm::with('boat', 'boatType')->find($id);
        if (!$form) {
            errBoatContactFormGet();
        }

        return success(BoatContactFormParser::first($form));
    }

    public function create(BoatContactFormRequest $request)
    {
        $algo = new BoatContactFormAlgo();
        return $algo->create($request);
    }

    public function delete($id)
    {
        $algo = new BoatContactFormAlgo((int)$id);
        return $algo->delete();
    }

    public function markAsRead($id)
    {
        $algo = new BoatContactFormAlgo((int)$id);
        return $algo->markAsRead();
    }

    public function changeStatus($id, ChangeMailStatusRequest $request)
    {
        $algo = new BoatContactFormAlgo((int)$id);
        return $algo->changeStatus($request);
    }
}
