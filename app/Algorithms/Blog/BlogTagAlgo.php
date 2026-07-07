<?php

namespace App\Algorithms\Blog;

use App\Models\Blog\BlogTag;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogTagAlgo
{
    public function __construct(protected BlogTag|int|null $blogTag = null)
    {
        if (is_int($this->blogTag)) {
            $this->blogTag = BlogTag::find($this->blogTag);
            if (!$this->blogTag) {
                errBlogTagGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->blogTag = BlogTag::create($request->all() + created_by());
                if (!$this->blogTag) {
                    errBlogTagSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->blogTag)
                    ->setType(ActivityType::BLOG_TAG)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new blog tag: " . $this->blogTag->name);

            });

            return success($this->blogTag);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->blogTag->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->blogTag)
                    ->setType(ActivityType::BLOG_TAG)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update blog tag: " . $this->blogTag->name);

            });

            return success($this->blogTag);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->blogTag->delete()) {
                    errBlogTagDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->blogTag)
                    ->setType(ActivityType::BLOG_TAG)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete blog tag: " . $this->blogTag->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}