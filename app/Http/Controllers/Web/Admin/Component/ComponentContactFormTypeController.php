<?php

namespace App\Http\Controllers\Web\Admin\Component;

use App\Algorithms\Component\ComponentAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Component\ComponentRequest;
use App\Models\Component\ComponentContactFormType;
use Illuminate\Http\Request;

class ComponentContactFormTypeController extends Controller
{
    public function get(Request $request)
    {
        $contactFormTypes = ComponentContactFormType::get();
        return success($contactFormTypes);
    }

    public function create(ComponentRequest $request)
    {
        $algo = new ComponentAlgo();
        return $algo->createBy(new ComponentContactFormType, $request);
    }

    public function update(ComponentContactFormType $componentContactFormType, ComponentRequest $request)
    {
        $algo = new ComponentAlgo();
        return $algo->update($componentContactFormType, $request);
    }

    public function delete(ComponentContactFormType $componentContactFormType)
    {
        $algo = new ComponentAlgo();
        return $algo->delete($componentContactFormType);
    }
}
