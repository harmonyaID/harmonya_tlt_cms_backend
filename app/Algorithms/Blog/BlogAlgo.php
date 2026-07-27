<?php

namespace App\Algorithms\Blog;

use App\Algorithms\Seo\ContentSeoAlgo;
use App\Models\Blog\Blog;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogAlgo
{
    public function __construct(protected Blog|int|null $blog = null)
    {
        if (is_int($this->blog)) {
            $this->blog = Blog::find($this->blog);
            if (!$this->blog) {
                errBlogGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $data = $request->except('thumbnail', 'tagIds');
                $data['slug'] = Str::slug($request->slug ?: $request->title);
                $data['publishedAt'] = $this->parsePublishedAt($request->publishedAt) ?? now();

                $this->blog = Blog::create($data + created_by());
                if (!$this->blog) {
                    errBlogSave();
                }

                if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                    $this->blog->thumbnail = $this->uploadThumbnail($request);
                    $this->blog->save();
                }

                if ($request->has('tagIds') && is_array($request->tagIds)) {
                    $this->blog->tags()->sync($request->tagIds);
                }

                (new ContentSeoAlgo($this->blog))->save($request);
                
                activity()->setCausedBy()
                    ->setReference($this->blog)
                    ->setType(ActivityType::BLOG)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new blog: " . $this->blog->title);
            });

            return success($this->blog->load('category', 'tags','seo'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $data = $request->except('thumbnail', 'tagIds');
                if ($request->has('slug')) {
                    $data['slug'] = Str::slug($request->slug);
                }
                if ($request->has('publishedAt')) {
                    $data['publishedAt'] = $this->parsePublishedAt($request->publishedAt);
                }

                $this->blog->update($data);

                if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                    $this->blog->thumbnail = $this->uploadThumbnail($request);
                    $this->blog->save();
                }

                if ($request->boolean('deleteThumbnail')) {
                    $dirPath = PathConstant::IMAGES_BLOG_STORAGE_PUBLIC_PATH();

                    if ($this->blog->thumbnail && file_exists($dirPath . $this->blog->thumbnail)) {
                        unlink($dirPath . $this->blog->thumbnail);
                    }

                    $this->blog->thumbnail = null;
                    $this->blog->save();
                }
                if ($request->has('tagIds') && is_array($request->tagIds)) {
                    $this->blog->tags()->sync($request->tagIds);
                }

                (new ContentSeoAlgo($this->blog))->save($request);

                activity()->setCausedBy()
                    ->setReference($this->blog)
                    ->setType(ActivityType::BLOG)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update blog: " . $this->blog->title);
            });

            return success($this->blog->load('category', 'tags','seo'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                $dirPath = PathConstant::IMAGES_BLOG_STORAGE_PUBLIC_PATH();
                if ($this->blog->thumbnail && file_exists($dirPath . $this->blog->thumbnail)) {
                    unlink($dirPath . $this->blog->thumbnail);
                }

                $this->blog->tags()->detach();

                if (!$this->blog->delete()) {
                    errBlogDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->blog)
                    ->setType(ActivityType::BLOG)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete blog: " . $this->blog->title);
            });

            return success();
        } catch (\Error $error) {
            exception($error);
        }
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    /**
     * Parse a 'd/m/Y H:i' formatted string into a Carbon instance.
     * Carbon's automatic parser assumes American m/d/Y order for slash-separated
     * dates, so a value like "27/07/2026 01:23" must be parsed explicitly.
     */
    private function parsePublishedAt(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y H:i', $value);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function uploadThumbnail(Request $request)
    {
        $image = $request->file('thumbnail');

        $dirPath = PathConstant::IMAGES_BLOG_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        if ($this->blog->thumbnail && file_exists($dirPath . $this->blog->thumbnail)) {
            unlink($dirPath . $this->blog->thumbnail);
        }

        $filename = filename($image, $this->blog->title);
        $image->move($dirPath, $filename);

        return $filename;
    }
}
