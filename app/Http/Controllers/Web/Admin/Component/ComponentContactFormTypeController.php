<?php

namespace App\Http\Controllers\Web\Admin\Component;

use App\Algorithms\Component\ComponentAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Component\ComponentRequest;
use App\Models\Component\ComponentContactFormType;
use Illuminate\Http\Request;

class ComponentContactFormTypeController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function get(Request $request)
    {
        $contactFormTypes = ComponentContactFormType::get();
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
        return $algo->createBy(new ComponentContactFormType, $request);
    }

    /**
     * @param ComponentContactFormType $componentContactFormType
     * @param ComponentRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update(ComponentContactFormType $componentContactFormType, ComponentRequest $request)
    {
        $algo = new ComponentAlgo();
        return $algo->update($componentContactFormType, $request);
    }

    /**
     * @param ComponentContactFormType $componentContactFormType
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function delete(ComponentContactFormType $componentContactFormType)
    {
        $algo = new ComponentAlgo();
        return $algo->delete($componentContactFormType);
    }

}
