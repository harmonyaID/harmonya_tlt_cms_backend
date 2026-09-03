<?php

namespace App\Http\Controllers\Web\Admin\TltTestimonial;

use App\Algorithms\TltTestimonial\TltTestimonialAlgo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasTrash;
use App\Http\Requests\TltTestimonial\TltTestimonialRequest;
use App\Models\TltTestimonial\TltTestimonial;
use App\Parser\TltTestimonial\TltTestimonialParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;

class TltTestimonialController extends Controller
{
    use HasTrash;

    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_TLT_TESTIMONIAL_VIEW);
                return $next($request);
            })->only(['get', 'detail', 'trash']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_TLT_TESTIMONIAL_CREATE);
                return $next($request);
            })->only(['create']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_TLT_TESTIMONIAL_UPDATE);
                return $next($request);
            })->only(['update']);

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_TLT_TESTIMONIAL_DELETE);
                return $next($request);
            })->only(['delete', 'restore', 'forceDelete']);
        }
    }

    public function get(Request $request)
    {
        $testimonials = TltTestimonial::filter($request)->getOrPaginate($request);
        return success(TltTestimonialParser::briefs($testimonials), pagination: pagination($testimonials));
    }

    public function detail($id)
    {
        $testimonial = TltTestimonial::find($id);
        if (!$testimonial) {
            errTltTestimonialGet();
        }

        return success(TltTestimonialParser::first($testimonial));
    }

    public function create(TltTestimonialRequest $request)
    {
        $algo = new TltTestimonialAlgo();
        return $algo->create($request);
    }

    public function update($id, TltTestimonialRequest $request)
    {
        $algo = new TltTestimonialAlgo((int)$id);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new TltTestimonialAlgo((int)$id);
        return $algo->delete();
    }

    protected function trashModel(): string
    {
        return TltTestimonial::class;
    }

    protected function trashParser(): string
    {
        return TltTestimonialParser::class;
    }

    protected function trashActivityType(): string
    {
        return ActivityType::TLT_TESTIMONIAL;
    }

    protected function trashLabel($item): string
    {
        return $item->name;
    }
}
