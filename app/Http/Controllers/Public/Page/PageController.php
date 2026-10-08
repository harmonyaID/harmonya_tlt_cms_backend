<?php

namespace App\Http\Controllers\Public\Page;

use App\Http\Controllers\Controller;
use App\Models\Page\Page;
use App\Parser\Page\PageParser;

class PageController extends Controller
{
    public function detail($idOrSlug)
    {

        $page = Page::bySlugOrId($idOrSlug)->with(['seo', 'acf'])->first();
       if (!$page) {
            errPageGet();
        }


        return success(PageParser::first($page));
    }
}
