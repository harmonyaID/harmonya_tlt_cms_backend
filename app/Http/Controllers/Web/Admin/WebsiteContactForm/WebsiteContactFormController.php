<?php

namespace App\Http\Controllers\Web\Admin\WebsiteContactForm;

use App\Algorithms\WebsiteContactForm\WebsiteContactFormAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeMailStatusRequest;
use App\Http\Requests\WebsiteContactForm\WebsiteContactFormRequest;
use App\Models\WebsiteContactForm\WebsiteContactForm;
use App\Parser\WebsiteContactForm\WebsiteContactFormParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class WebsiteContactFormController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_WEBSITE_CONTACT_FORM_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_WEBSITE_CONTACT_FORM_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_WEBSITE_CONTACT_FORM_UPDATE);
                return $next($request);
            })->only(['update', 'markAsRead']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_WEBSITE_CONTACT_FORM_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $contactForms = WebsiteContactForm::filter($request)->getOrPaginate($request);
        return success(WebsiteContactFormParser::briefs($contactForms), pagination: pagination($contactForms));
    }

    public function detail($id)
    {
        $contactForm = WebsiteContactForm::find($id);
        if (!$contactForm) {
            errWebsiteContactFormGet();
        }

        return success(WebsiteContactFormParser::first($contactForm));
    }

    public function create(WebsiteContactFormRequest $request)
    {
        $algo = new WebsiteContactFormAlgo();
        return $algo->create($request);
    }

    public function update($id, WebsiteContactFormRequest $request)
    {
        $algo = new WebsiteContactFormAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new WebsiteContactFormAlgo((int)$id);
        return $algo->delete();
    }

    public function markAsRead($id)
    {
        $algo = new WebsiteContactFormAlgo((int)$id);
        return $algo->markAsRead();
    }

    public function changeStatus($id, ChangeMailStatusRequest $request) {
        $algo = new WebsiteContactFormAlgo((int)$id);
        return $algo->changeStatus($request);
    }
}
