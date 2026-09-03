<?php

namespace App\Algorithms\Offer;

use App\Algorithms\Acf\ContentAcfAlgo;
use App\Algorithms\Seo\ContentSeoAlgo;
use App\Models\Offer\Offer;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OfferAlgo
{
    public function __construct(protected Offer|int|null $offer = null)
    {
        if (is_int($this->offer)) {
            $this->offer = Offer::find($this->offer);
            if (!$this->offer) {
                errOfferGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $data = $request->except('thumbnail', 'propertyIds', 'seo', 'acf');
                $data['slug'] = Str::slug($request->slug ?: $request->title);
                $data['publishedAt'] = $request->publishedAt ?: now();

                $this->offer = Offer::create($data + created_by());
                if (!$this->offer) {
                    errOfferSave();
                }

                if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                    $this->offer->thumbnail = $this->uploadThumbnail($request);
                    $this->offer->save();
                }

                if ($request->has('propertyIds') && is_array($request->propertyIds)) {
                    $this->offer->properties()->sync($request->propertyIds);
                }

                (new ContentSeoAlgo($this->offer))->save($request);
                (new ContentAcfAlgo($this->offer))->save($request);

                activity()->setCausedBy()
                    ->setReference($this->offer)
                    ->setType(ActivityType::OFFER)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new offer: " . $this->offer->title);
            });

            return success($this->offer->load('properties', 'seo', 'acf'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $data = $request->except('thumbnail', 'propertyIds', 'seo', 'acf');
                if ($request->has('slug')) {
                    $data['slug'] = Str::slug($request->slug);
                }

                $this->offer->update($data);

                if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
                    $this->offer->thumbnail = $this->uploadThumbnail($request);
                    $this->offer->save();
                }

                if ($request->boolean('deleteThumbnail')) {
                    $dirPath = PathConstant::IMAGES_OFFER_STORAGE_PUBLIC_PATH();

                    if ($this->offer->thumbnail && file_exists($dirPath . $this->offer->thumbnail)) {
                        unlink($dirPath . $this->offer->thumbnail);
                    }

                    $this->offer->thumbnail = null;
                    $this->offer->save();
                }

                if ($request->has('propertyIds') && is_array($request->propertyIds)) {
                    $this->offer->properties()->sync($request->propertyIds);
                }

                (new ContentSeoAlgo($this->offer))->save($request);
                (new ContentAcfAlgo($this->offer))->save($request);

                activity()->setCausedBy()
                    ->setReference($this->offer)
                    ->setType(ActivityType::OFFER)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update offer: " . $this->offer->title);
            });

            return success($this->offer->load('properties', 'seo', 'acf'));
        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                $dirPath = PathConstant::IMAGES_OFFER_STORAGE_PUBLIC_PATH();
                if ($this->offer->thumbnail && file_exists($dirPath . $this->offer->thumbnail)) {
                    unlink($dirPath . $this->offer->thumbnail);
                }

                $this->offer->properties()->detach();
                $this->offer->acf()->delete();

                if (!$this->offer->delete()) {
                    errOfferDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->offer)
                    ->setType(ActivityType::OFFER)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete offer: " . $this->offer->title);
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

    private function uploadThumbnail(Request $request)
    {
        $image = $request->file('thumbnail');

        $dirPath = PathConstant::IMAGES_OFFER_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        if ($this->offer->thumbnail && file_exists($dirPath . $this->offer->thumbnail)) {
            unlink($dirPath . $this->offer->thumbnail);
        }

        $filename = filename($image, $this->offer->title);
        $image->move($dirPath, $filename);

        return $filename;
    }
}
