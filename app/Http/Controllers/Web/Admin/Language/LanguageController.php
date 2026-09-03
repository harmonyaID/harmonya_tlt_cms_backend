<?php

namespace App\Http\Controllers\Web\Admin\Language;

use App\Algorithms\Language\LanguageAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Language\LanguageMainRequest;
use App\Http\Requests\Language\LanguageRequest;
use App\Models\Language\Language;
use App\Parser\Language\LanguageParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            // VIEW
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_LANGUAGE_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            // CREATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_LANGUAGE_CREATE);
                return $next($request);
            })->only(['create']);

            // UPDATE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_LANGUAGE_UPDATE);
                return $next($request);
            })->only(['update', 'activation', 'changePassword']);

            // DELETE
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_LANGUAGE_CREATE);
                return $next($request);
            })->only(['delete']);

            // ADMIN
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_LANGUAGE);
                return $next($request);
            })->only(['updateSuperadmin']);

        }
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $languages = Language::filter($request)->orderBy('country', 'ASC')->getOrPaginate($request);
        return success(LanguageParser::briefs($languages), pagination: pagination($languages));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function detail($id)
    {
        
    }

    /**
     * @param LanguageRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function create(LanguageRequest $request)
    {
        $algo = new LanguageAlgo();
        return $algo->create($request);
    }

    /**
     * @param $id
     * @param LanguageRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update($id, LanguageRequest $request)
    {
        $algo = new LanguageAlgo((int)$id);
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
        $algo = new LanguageAlgo((int)$id);
        return $algo->delete();
    }

    /**
     * @param Language $language
     * @param LanguageMainRequest $request
     * [
     *      'main' => false',
     * ]
     *
     * @return ResponseBuilderData|JsonResponse|mixed
     */
    public function changeMainStatus($id, LanguageMainRequest $request)
    {
        $algo = new LanguageAlgo((int)$id);
        return $algo->changeMain($$request->main);
    }
}
