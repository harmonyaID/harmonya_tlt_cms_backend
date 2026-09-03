<?php

namespace App\Algorithms\Homepage;

use App\Algorithms\Seo\ContentSeoAlgo;
use App\Models\Homepage\Homepage;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class HomepageAlgo
{
    public function __construct(protected Homepage|int|null $homepage = null)
    {
        if (is_int($this->homepage)) {
            $this->homepage = Homepage::find($this->homepage);
            if (!$this->homepage) {
                errHomepageGet();
            }
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $oldValue = $this->homepage->value ?? [];

                $value = $request->input('value', []);
                $files = $request->file('value', []);

                if ($files) {
                    $value = $this->uploadValueImages($value, $files, $oldValue);
                }

                $data = $request->except('seo', 'value');
                $data['value'] = $value;

                if (!$this->homepage->update($data)) {
                    errHomepageUpdate();
                }

                (new ContentSeoAlgo($this->homepage))->save($request);

                activity()->setCausedBy()
                    ->setReference($this->homepage)
                    ->setType(ActivityType::HOMEPAGE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update homepage content");
            });

            return success($this->homepage->load('seo'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |--------------------------------------------------------------------------
     */

    private function uploadValueImages(array $value, array $files, array $oldValue): array
    {
        foreach ($files as $key => $item) {

            if ($item instanceof UploadedFile) {

                if (!$item->isValid()) {
                    continue;
                }

                $old = is_array($oldValue) ? ($oldValue[$key] ?? null) : null;

                // old value at this path isn't a plain filename (e.g. it was
                // still an array/object) - nothing sensible to delete/reuse.
                if (!is_string($old)) {
                    $old = null;
                }

                $value[$key] = $this->uploadFile($item, $old);

                continue;
            }

            if (is_array($item)) {
                $currentValue = is_array($value[$key] ?? null) ? $value[$key] : [];
                $currentOld = is_array($oldValue[$key] ?? null) ? $oldValue[$key] : [];

                $value[$key] = $this->uploadValueImages($currentValue, $item, $currentOld);
            }
        }

        return $value;
    }

    private function uploadFile(UploadedFile $image, ?string $oldFilename = null): string
    {
        $dirPath = PathConstant::IMAGES_HOMEPAGE_STORAGE_PUBLIC_PATH();

        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $this->deleteFile($oldFilename);

        $filename = filename($image, 'homepage');
        $image->move($dirPath, $filename);

        return $filename;
    }

    private function deleteFile(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $filename = basename($filename);

        $dirPath = PathConstant::IMAGES_HOMEPAGE_STORAGE_PUBLIC_PATH();

        if (file_exists($dirPath . $filename)) {
            unlink($dirPath . $filename);
        }
    }
}