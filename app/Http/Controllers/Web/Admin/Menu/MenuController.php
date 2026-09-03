<?php

namespace App\Http\Controllers\Web\Admin\Menu;

use App\Algorithms\Menu\MenuAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\MenuRequest;
use App\Models\Menu\Menu;
use App\Parser\Menu\MenuParser;
use App\Services\Constant\Access\AccessPermissionName;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_MENU_VIEW);
                return $next($request);
            })->only(['get', 'detail']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_MENU_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_MENU_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_MENU_DELETE);
                return $next($request);
            })->only(['delete']);
        }
    }

    public function get(Request $request)
    {
        $menus = Menu::filter($request)->getOrPaginate($request);
        return success(MenuParser::briefs($menus), pagination: pagination($menus));
    }

    public function detail($id)
    {
        $menu = Menu::with('rootItems.children')->find($id);
        if (!$menu) errMenuGet();
        return success(MenuParser::first($menu));
    }

    public function create(MenuRequest $request)
    {
        return (new MenuAlgo())->create($request);
    }

    public function update($id, MenuRequest $request)
    {
        return (new MenuAlgo((int)$id))->update($request);
    }

    public function delete($id)
    {
        return (new MenuAlgo((int)$id))->delete();
    }
}