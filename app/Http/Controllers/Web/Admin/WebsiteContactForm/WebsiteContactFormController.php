<?php

namespace App\Http\Controllers\Web\Admin\WebsiteContactForm;

use App\Algorithms\WebsiteContactForm\WebsiteContactFormAlgo;
use App\Http\Controllers\Controller;
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

            // VIEW
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_WEBSITE_CONTACT_FORM_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            // CREATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_WEBSITE_CONTACT_FORM_CREATE);
                return $next($request);
            })->only(['create']);

            // UPDATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_WEBSITE_CONTACT_FORM_UPDATE);
                return $next($request);
            })->only(['update', 'markAsRead']);

            // DELETE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_WEBSITE_CONTACT_FORM_DELETE);
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
        $contactForms = WebsiteContactForm::filter($request)->getOrPaginate($request);
        return success(WebsiteContactFormParser::briefs($contactForms), pagination: pagination($contactForms));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function detail($id)
    {
        $contactForm = WebsiteContactForm::find($id);
        if (!$contactForm) {
            errWebsiteContactFormGet();
        }

        return success(WebsiteContactFormParser::first($contactForm));
    }

    /**
     * @param WebsiteContactFormRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function create(WebsiteContactFormRequest $request)
    {
        $algo = new WebsiteContactFormAlgo();
        return $algo->create($request);
    }

    /**
     * @param $id
     * @param WebsiteContactFormRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update($id, WebsiteContactFormRequest $request)
    {
        $algo = new WebsiteContactFormAlgo((int)$id);
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
        $algo = new WebsiteContactFormAlgo((int)$id);
        return $algo->delete();
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function markAsRead($id)
    {
        $algo = new WebsiteContactFormAlgo((int)$id);
        return $algo->markAsRead();
    }
}