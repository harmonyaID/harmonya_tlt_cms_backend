<?php

namespace App\Algorithms\Blog;

use App\Models\Blog\BlogCategory;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogCategoryAlgo
{
    public function __construct(protected BlogCategory|int|null $blogCategory = null)
    {
        if (is_int($this->blogCategory)) {
            $this->blogCategory = BlogCategory::find($this->blogCategory);
            if (!$this->blogCategory) {
                errBlogCategoryGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->blogCategory = BlogCategory::create($request->all() + created_by());
                if (!$this->blogCategory) {
                    errBlogCategorySave();
                }

                activity()->setCausedBy()
                    ->setReference($this->blogCategory)
                    ->setType(ActivityType::BLOG_CATEGORY)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new blog category: " . $this->blogCategory->name);

            });

            return success($this->blogCategory);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->blogCategory->update($request->all());

                activity()->setCausedBy()
                    ->setReference($this->blogCategory)
                    ->setType(ActivityType::BLOG_CATEGORY)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update blog category: " . $this->blogCategory->name);

            });

            return success($this->blogCategory);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->blogCategory->delete()) {
                    errBlogCategoryDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->blogCategory)
                    ->setType(ActivityType::BLOG_CATEGORY)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete blog category: " . $this->blogCategory->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}