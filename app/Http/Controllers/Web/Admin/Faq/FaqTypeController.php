<?php

namespace App\Http\Controllers\Web\Admin\Faq;

use App\Algorithms\Faq\FaqTypeAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\Component\ComponentRequest;
use App\Models\Faq\FaqType;
use App\Parser\Faq\FaqTypeParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class FaqTypeController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_FAQ_TYPE_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_FAQ_TYPE_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_FAQ_TYPE_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_FAQ_TYPE_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);
        }
    }

    public function get(Request $request)
    {
        $types = FaqType::filter($request)->getOrPaginate($request);
        return success(FaqTypeParser::briefs($types), pagination: pagination($types));
    }

    public function detail($id)
    {
        $type = FaqType::find($id);
        if (!$type) {
            errFaqTypeGet();
        }

        return success(FaqTypeParser::first($type));
    }

    public function create(ComponentRequest $request)
    {
        $algo = new FaqTypeAlgo();
        return $algo->create($request);
    }

    public function update($id, ComponentRequest $request)
    {
        $algo = new FaqTypeAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new FaqTypeAlgo((int)$id);
        return $algo->delete();
    }

    protected function trashModel(): string
    {
        return FaqType::class;
    }

    protected function trashParser(): string
    {
        return FaqTypeParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::FAQ_TYPE;
    }

    protected function trashLabel($item): string
    {
        return $item->name;
    }
}
