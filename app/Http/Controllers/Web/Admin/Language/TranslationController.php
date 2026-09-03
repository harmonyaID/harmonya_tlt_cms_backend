<?php

namespace App\Http\Controllers\Web\Admin\Language;

use App\Algorithms\Staff\StaffAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\ActivationRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Staff\StaffRequest;
use App\Models\Language\Translation;
use App\Models\Staff\Staff;
use App\Parser\Staff\StaffParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $staffs = Staff::filter($request)->getOrPaginate($request);
        return success(StaffParser::briefs($staffs), pagination: pagination($staffs));
    }

    /**
     * @param $id
     * @param StaffRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update($id, StaffRequest $request)
    {
        $algo = new StaffAlgo((int)$id);
        return $algo->update($request);
    }
    
    /**
     * @param Translation $translation
     * @param string $locale
     * @param Request $request
     * [
     *      'sentences' => 'Hapus'
     * ]
     *
     * @return mixed
     */
    public function updateSpecificLang($id, $locale, Request $request)
    {
        $algo = new StaffAlgo((int)$id);
        return $algo->update($request);
    }
}
