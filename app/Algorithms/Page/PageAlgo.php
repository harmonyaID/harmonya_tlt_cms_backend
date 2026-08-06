<?php

namespace App\Algorithms\Page;

use App\Algorithms\Acf\ContentAcfAlgo;
use App\Algorithms\Seo\ContentSeoAlgo;
use App\Models\Page\Page;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageAlgo
{
    public function __construct(protected Page|int|null $page = null)
    {
        if (is_int($this->page)) {
            $this->page = Page::find($this->page);
            if (!$this->page) {
                errPageGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $data = $request->except(['featuredImage', 'deleteFeaturedImage', 'seo', 'acf']);

                $this->page = Page::create($data + created_by());
                if (!$this->page) {
                    errPageSave();
                }

                if (!$this->page->groupId) {
                    $this->page->groupId = $this->page->id;
                    $this->page->save();
                }

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->page->featuredImage = $this->uploadImage($request);
                    $this->page->save();
                }

                (new ContentSeoAlgo($this->page))->save($request);
                (new ContentAcfAlgo($this->page))->save($request);

                activity()->setCausedBy()
                    ->setReference($this->page)
                    ->setType(ActivityType::PAGE)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new page: " . $this->page->title);
            });

            return success($this->page->load('seo', 'acf', 'createdBy'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $data = $request->except(['featuredImage', 'deleteFeaturedImage', 'seo', 'acf']);

                $this->page->update($data);

                if ($request->boolean('deleteFeaturedImage')) {
                    $this->deleteImage($this->page->featuredImage);
                    $this->page->featuredImage = null;
                    $this->page->save();
                }

                if ($request->hasFile('featuredImage') && $request->file('featuredImage')->isValid()) {
                    $this->page->featuredImage = $this->uploadImage($request);
                    $this->page->save();
                }

                (new ContentSeoAlgo($this->page))->save($request);
                (new ContentAcfAlgo($this->page))->save($request);

                activity()->setCausedBy()
                    ->setReference($this->page)
                    ->setType(ActivityType::PAGE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update page: " . $this->page->title);
            });

            return success($this->page->load('seo', 'acf', 'createdBy'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                $this->deleteImage($this->page->featuredImage);
                $this->page->acf()->delete();
                if ($this->page->seo) {
                    $this->page->seo()->delete();
                }

                if (!$this->page->delete()) {
                    errPageDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->page)
                    ->setType(ActivityType::PAGE)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete page: " . $this->page->title);
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

    private function uploadImage(Request $request): string
    {
        $image = $request->file('featuredImage');

        $dirPath = PathConstant::IMAGES_PAGE_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $this->deleteImage($this->page->featuredImage);

        $filename = filename($image, $this->page->title);
        $image->move($dirPath, $filename);

        return $filename;
    }

    private function deleteImage(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $dirPath = PathConstant::IMAGES_PAGE_STORAGE_PUBLIC_PATH();
        if (file_exists($dirPath . $filename)) {
            unlink($dirPath . $filename);
        }
    }
}
