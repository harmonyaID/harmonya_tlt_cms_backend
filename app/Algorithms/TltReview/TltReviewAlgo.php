<?php

namespace App\Algorithms\TltReview;

use App\Models\TltReview\TltReview;
use App\Models\TltReview\TltReviewPhoto;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TltReviewAlgo
{
    /**
     * @param TltReview|int|null $review
     */
    public function __construct(protected TltReview|int|null $review = null)
    {
        if (is_int($this->review)) {
            $this->review = TltReview::find($this->review);
            if (!$this->review) {
                errTltReviewGet();
            }
        }
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->review = TltReview::create($request->except('photos', 'deletePhotoIds'));
                if (!$this->review) {
                    errTltReviewSave();
                }

                if ($request->hasFile('photos')) {
                    $this->uploadPhotos($request);
                }

                activity()->setCausedBy()
                    ->setReference($this->review)
                    ->setType(ActivityType::TLT_REVIEW)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new review. Name: " . $this->review->name);

            });

            return success($this->review->load('photos'));

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->review->update($request->except('photos', 'deletePhotoIds'));

                if ($request->has('deletePhotoIds')) {
                    $this->deletePhotos($request->deletePhotoIds);
                }

                if ($request->hasFile('photos')) {
                    $this->uploadPhotos($request);
                }

                activity()->setCausedBy()
                    ->setReference($this->review)
                    ->setType(ActivityType::TLT_REVIEW)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update review. Name: " . $this->review->name);

            });

            return success($this->review->load('photos'));

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function delete()
    {
        try {

            DB::transaction(function () {

                $dirPath = PathConstant::IMAGES_TLT_REVIEW_STORAGE_PUBLIC_PATH();
                foreach ($this->review->photos as $photo) {
                    if (file_exists($dirPath . $photo->photo)) {
                        unlink($dirPath . $photo->photo);
                    }
                    $photo->delete();
                }

                if (!$this->review->delete()) {
                    errTltReviewDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->review)
                    ->setType(ActivityType::TLT_REVIEW)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete review. Name: " . $this->review->name);

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

    private function uploadPhotos(Request $request)
    {
        $dirPath = PathConstant::IMAGES_TLT_REVIEW_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        foreach ($request->file('photos') as $photo) {
            if (!$photo->isValid()) {
                continue;
            }

            $filename = filename($photo, $this->review->name);
            $photo->move($dirPath, $filename);

            TltReviewPhoto::create([
                'reviewId' => $this->review->id,
                'photo' => $filename,
            ]);
        }
    }

    private function deletePhotos(array $photoIds)
    {
        $dirPath = PathConstant::IMAGES_TLT_REVIEW_STORAGE_PUBLIC_PATH();

        $photos = TltReviewPhoto::where('reviewId', $this->review->id)
            ->whereIn('id', $photoIds)
            ->get();

        foreach ($photos as $photo) {
            if (file_exists($dirPath . $photo->photo)) {
                unlink($dirPath . $photo->photo);
            }
            $photo->delete();
        }
    }
}