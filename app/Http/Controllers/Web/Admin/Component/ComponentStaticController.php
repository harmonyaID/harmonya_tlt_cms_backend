<?php

namespace App\Http\Controllers\Web\Admin\Component;

use App\Http\Controllers\Controller;
use App\Services\Constant\Global\MailStatus;
use Illuminate\Http\Request;

class ComponentStaticController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getStatusForm()
    {
        return success(MailStatus::get());
    }

}
