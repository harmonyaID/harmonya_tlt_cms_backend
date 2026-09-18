<?php

namespace App\Algorithms\Page;

use App\Algorithms\Seo\ContentSeoAlgo;
use App\Models\Page\Page;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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

                $content = $request->input('content', []);
                $files = $request->file('content', []);

                if (!is_array($content)) {
                    $content = [];
                }

                if ($files) {
                    $content = $this->uploadContentImages(
                        $content,
                        $files,
                        []
                    );
                }

                $data = $request->except([
                    'content',
                    'featuredImage',
                    'deleteFeaturedImage',
                    'seo',
                    'acf',
                ]);

                $data['content'] = $content;

                $this->page = Page::create($data + created_by());

                if (!$this->page) {
                    errPageSave();
                }

                if (!$this->page->groupId) {
                    $this->page->groupId = $this->page->id;
                    $this->page->save();
                }

                if (
                    $request->hasFile('featuredImage') &&
                    $request->file('featuredImage')->isValid()
                ) {
                    $this->page->featuredImage = $this->uploadImage($request);
                    $this->page->save();
                }

                (new ContentSeoAlgo($this->page))->save($request);

                activity()->setCausedBy()
                    ->setReference($this->page)
                    ->setType(ActivityType::PAGE)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new page: " . $this->page->title);
            });

            return success(
                $this->page->load(
                    'seo',
                    'createdBy'
                )
            );
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $oldContent = $this->page->content ?? [];

                $content = $request->input('content', []);
                $files = $request->file('content', []);

                if (!is_array($content)) {
                    $content = [];
                }

                if ($files) {
                    $content = $this->uploadContentImages(
                        $content,
                        $files,
                        is_array($oldContent) ? $oldContent : []
                    );
                }

                $data = $request->except([
                    'content',
                    'featuredImage',
                    'deleteFeaturedImage',
                    'seo',
                    'acf',
                ]);

                if ($request->has('content')) {
                    $data['content'] = $content;
                }

                if (!$this->page->update($data)) {
                    errPageUpdate();
                }

                if ($request->boolean('deleteFeaturedImage')) {
                    $this->deleteImage(
                        $this->page->featuredImage
                    );

                    $this->page->featuredImage = null;
                    $this->page->save();
                }

                if (
                    $request->hasFile('featuredImage') &&
                    $request->file('featuredImage')->isValid()
                ) {
                    $this->page->featuredImage = $this->uploadImage($request);
                    $this->page->save();
                }

                (new ContentSeoAlgo($this->page))->save($request);

                activity()->setCausedBy()
                    ->setReference($this->page)
                    ->setType(ActivityType::PAGE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update page: " . $this->page->title);
            });

            return success(
                $this->page->load(
                    'seo',
                    'createdBy'
                )
            );
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                $this->deleteImage(
                    $this->page->featuredImage
                );

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

    private function uploadContentImages(
        array $content,
        array $files,
        array $oldContent
    ): array {
        foreach ($files as $key => $item) {

            if ($item instanceof UploadedFile) {

                if (!$item->isValid()) {
                    continue;
                }

                $old = is_array($oldContent)
                    ? ($oldContent[$key] ?? null)
                    : null;

                if (!is_string($old)) {
                    $old = null;
                }

                $content[$key] = $this->uploadContentFile(
                    $item,
                    $old
                );

                continue;
            }

            if (is_array($item)) {

                $currentContent = is_array($content[$key] ?? null)
                    ? $content[$key]
                    : [];

                $currentOldContent = is_array($oldContent[$key] ?? null)
                    ? $oldContent[$key]
                    : [];

                $content[$key] = $this->uploadContentImages(
                    $currentContent,
                    $item,
                    $currentOldContent
                );
            }
        }

        return $content;
    }

    private function uploadContentFile(
        UploadedFile $image,
        ?string $oldFilename = null
    ): string {
        $dirPath = PathConstant::IMAGES_PAGE_STORAGE_PUBLIC_PATH();

        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $this->deleteImage($oldFilename);

        $filename = filename(
            $image,
            'page'
        );

        $image->move(
            $dirPath,
            $filename
        );

        return asset(
            'storage/images/pages/' . $filename
        );
    }
    
    private function uploadImage(Request $request): string
    {
        $image = $request->file('featuredImage');

        $dirPath = PathConstant::IMAGES_PAGE_STORAGE_PUBLIC_PATH();

        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $this->deleteImage(
            $this->page->featuredImage
        );

        $filename = filename(
            $image,
            $this->page->title
        );

        $image->move(
            $dirPath,
            $filename
        );

        return $filename;
    }

    private function deleteImage(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $filename = basename($filename);

        $dirPath = PathConstant::IMAGES_PAGE_STORAGE_PUBLIC_PATH();

        if (file_exists($dirPath . $filename)) {
            unlink($dirPath . $filename);
        }
    }
}
