<?php

namespace App\Http\Controllers\Public\Page;

use App\Http\Controllers\Controller;
use App\Models\Page\Page;
use App\Parser\Page\PageParser;

class PageController extends Controller
{
    public function detail($id)
    {
        $page = Page::where('isActive', true)->find($id);
        if (!$page) {
            errPageGet();
        }

        return success(PageParser::first($page));
    }
}
