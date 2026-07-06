<?php

namespace App\Algorithms\Boat;

use App\Models\Boat\Boat;
use App\Models\Boat\BoatPhoto;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoatAlgo
{
    public function __construct(protected Boat|int|null $boat = null)
    {
        if (is_int($this->boat)) {
            $this->boat = Boat::find($this->boat);
            if (!$this->boat) {
                errBoatGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->boat = Boat::create($request->except(['photos', 'promoPhotos', 'priceFile', 'deletePhotoIds', 'customInformations']));
                if (!$this->boat) {
                    errBoatSave();
                }

                if ($request->hasFile('promoPhotos')) {
                    $this->boat->promoPhotos = $this->uploadPromoPhotos($request);
                    $this->boat->save();
                }

                if ($request->hasFile('priceFile')) {
                    $this->boat->priceFile = $this->uploadPriceFile($request);
                    $this->boat->save();
                }

                if ($request->hasFile('photos')) {
                    $this->uploadPhotos($request);
                }

                if ($request->has('customInformations')) {
                    $this->syncCustomInformations($request);
                }

                activity()->setCausedBy()
                    ->setReference($this->boat)
                    ->setType(ActivityType::BOAT)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Create new Boat. ID: " . $this->boat->id);

            });

            return success($this->boat->load(['photos', 'customInformations']));

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->boat->update($request->except(['photos', 'promoPhotos', 'priceFile', 'deletePhotoIds', 'customInformations']));

                if ($request->hasFile('promoPhotos')) {
                    $this->deletePromoPhotos();
                    $this->boat->promoPhotos = $this->uploadPromoPhotos($request);
                    $this->boat->save();
                }

                if ($request->hasFile('priceFile')) {
                    $this->deletePriceFile();
                    $this->boat->priceFile = $this->uploadPriceFile($request);
                    $this->boat->save();
                }

                if ($request->has('deletePhotoIds')) {
                    $this->deletePhotos($request->deletePhotoIds);
                }

                if ($request->hasFile('photos')) {
                    $this->uploadPhotos($request);
                }

                if ($request->has('customInformations')) {
                    $this->syncCustomInformations($request);
                }

                activity()->setCausedBy()
                    ->setReference($this->boat)
                    ->setType(ActivityType::BOAT)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update Boat. ID: " . $this->boat->id);

            });

            return success($this->boat->load(['photos', 'customInformations']));

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                $dirPath = PathConstant::IMAGES_BOAT_STORAGE_PUBLIC_PATH();
                foreach ($this->boat->photos as $photo) {
                    if (file_exists($dirPath . $photo->photo)) {
                        unlink($dirPath . $photo->photo);
                    }
                    $photo->delete();
                }

                $this->deletePromoPhotos();
                $this->deletePriceFile();

                $this->boat->customInformations()->delete();

                if (!$this->boat->delete()) {
                    errBoatDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->boat)
                    ->setType(ActivityType::BOAT)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete Boat. ID: " . $this->boat->id);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }

    /*
     |--------------------------------------------------------------------------
     | Private Helpers
     |-------------------------------------------------------------------------
     */

    private function uploadPhotos(Request $request): void
    {
        $dirPath = PathConstant::IMAGES_BOAT_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        foreach ($request->file('photos') as $index => $photo) {
            if (!$photo->isValid()) continue;

            $filename = filename($photo, 'boat-' . $this->boat->id);
            $photo->move($dirPath, $filename);

            BoatPhoto::create([
                'boatId' => $this->boat->id,
                'photo'  => $filename,
                'order'  => $index,
            ]);
        }
    }

    private function deletePhotos(array $photoIds): void
    {
        $dirPath = PathConstant::IMAGES_BOAT_STORAGE_PUBLIC_PATH();

        $photos = BoatPhoto::where('boatId', $this->boat->id)
            ->whereIn('id', $photoIds)
            ->get();

        foreach ($photos as $photo) {
            if (file_exists($dirPath . $photo->photo)) {
                unlink($dirPath . $photo->photo);
            }
            $photo->delete();
        }
    }

    private function uploadPromoPhotos(Request $request): array
    {
        $dirPath = PathConstant::IMAGES_BOAT_PROMO_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $filenames = [];
        foreach ($request->file('promoPhotos') as $photo) {
            if (!$photo->isValid()) continue;

            $filename = filename($photo, 'boat-promo-' . $this->boat->id);
            $photo->move($dirPath, $filename);
            $filenames[] = $filename;
        }

        return $filenames;
    }

    private function deletePromoPhotos(): void
    {
        $dirPath = PathConstant::IMAGES_BOAT_PROMO_STORAGE_PUBLIC_PATH();
        foreach ($this->boat->promoPhotos ?? [] as $photo) {
            if (file_exists($dirPath . $photo)) {
                unlink($dirPath . $photo);
            }
        }
    }

    private function uploadPriceFile(Request $request): string
    {
        $dirPath = PathConstant::FILES_BOAT_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $file = $request->file('priceFile');
        $filename = filename($file, 'boat-price-' . $this->boat->id);
        $file->move($dirPath, $filename);

        return $filename;
    }

    private function deletePriceFile(): void
    {
        if (!$this->boat->priceFile) return;

        $path = PathConstant::FILES_BOAT_STORAGE_PUBLIC_PATH() . $this->boat->priceFile;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    private function syncCustomInformations(Request $request): void
    {
        $incoming   = collect($request->input('customInformations', []));
        $incomingIds = $incoming->pluck('id')->filter()->values()->toArray();

        // hapus yang tidak dikirim
        $this->boat->customInformations()
            ->whereNotIn('id', $incomingIds)
            ->delete();

        foreach ($incoming as $item) {
            $this->boat->customInformations()->updateOrCreate(
                ['id' => $item['id'] ?? null],
                [
                    'boatId' => $this->boat->id,
                    'name'   => $item['name'],
                    'value'  => $item['value'],
                    'order'  => $item['order'] ?? 0,
                ]
            );
        }
    }
}