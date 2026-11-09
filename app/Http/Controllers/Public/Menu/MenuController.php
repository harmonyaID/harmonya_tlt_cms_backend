<?php

namespace App\Http\Controllers\Public\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu\Menu;
use App\Parser\Menu\MenuParser;

class MenuController extends Controller
{
    public function detail($handle)
    {
        $menu = Menu::where('handle', $handle)->with('rootItems.children')->first();
        if (!$menu) {
            errMenuGet();
        }

        return success(MenuParser::first($menu));
    }
}
