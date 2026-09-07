<?php

namespace App\Http\Controllers\Public\Visitor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Visitor\TrackVisitorRequest;
use App\Models\Blog\Blog;
use App\Models\Visitor\ContentVisitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    const CONTENT_TYPE_MAP = [
        'blog' => Blog::class,
    ];
    
    public function track(TrackVisitorRequest $request)
    {
        $modelClass = self::CONTENT_TYPE_MAP[$request->contentType];

        $contentable = $modelClass::find($request->contentId);
        if (!$contentable) {
            error(404, ucfirst($request->contentType) . ' not found');
        }

        ContentVisitor::recordVisit($contentable, $request->ip(), $request->userAgent());

        return success([
            'uniqueVisitorCount' => $contentable->uniqueVisitorCount(),
        ]);
    }
}
