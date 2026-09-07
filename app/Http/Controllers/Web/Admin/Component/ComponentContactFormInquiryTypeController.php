<?php

namespace App\Http\Controllers\Web\Admin\Component;

use App\Algorithms\Component\ComponentAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Component\ComponentRequest;
use App\Models\Component\ComponentContactFormInquiryType;
use Illuminate\Http\Request;

class ComponentContactFormInquiryTypeController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $contactFormTypes = ComponentContactFormInquiryType::get();
        return success($contactFormTypes);
    }

    /**
     * @param ComponentRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function create(ComponentRequest $request)
    {
        $algo = new ComponentAlgo();
        return $algo->createBy(new ComponentContactFormInquiryType, $request);
    }

    /**
     * @param ComponentContactFormInquiryType $ComponentContactFormInquiryType
     * @param ComponentRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update(ComponentContactFormInquiryType $ComponentContactFormInquiryType, ComponentRequest $request)
    {
        $algo = new ComponentAlgo();
        return $algo->update($ComponentContactFormInquiryType, $request);
    }

    /**
     * @param ComponentContactFormInquiryType $ComponentContactFormInquiryType
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function delete(ComponentContactFormInquiryType $ComponentContactFormInquiryType)
    {
        $algo = new ComponentAlgo();
        return $algo->delete($ComponentContactFormInquiryType);
    }

}
