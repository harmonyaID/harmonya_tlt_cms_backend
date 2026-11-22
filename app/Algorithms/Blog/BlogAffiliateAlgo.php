<?php

namespace App\Algorithms\Blog;

use App\Models\Blog\BlogAffiliate;
use App\Parser\Blog\BlogAffiliateParser;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogAffiliateAlgo
{
    public function __construct(protected BlogAffiliate|int|null $affiliate = null)
    {
        if (is_int($this->affiliate)) {
            $this->affiliate = BlogAffiliate::find($this->affiliate);
            if (!$this->affiliate) {
                errBlogAffiliateGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->affiliate = BlogAffiliate::create($this->attributes($request) + ['isAppliedToAll' => false]);
                if (!$this->affiliate) {
                    errBlogAffiliateSave();
                }

                $this->syncBannerImage($request);

                activity()->setCausedBy()
                    ->setReference($this->affiliate)
                    ->setType(ActivityType::BLOG_AFFILIATE)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new blog affiliate: " . $this->affiliate->title);

            });

            return success(BlogAffiliateParser::first($this->affiliate->fresh()));

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->affiliate->update($this->attributes($request));

                $this->syncBannerImage($request);

                activity()->setCausedBy()
                    ->setReference($this->affiliate)
                    ->setType(ActivityType::BLOG_AFFILIATE)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update blog affiliate: " . $this->affiliate->title);

            });

            return success(BlogAffiliateParser::first($this->affiliate->fresh()));

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->affiliate->delete()) {
                    errBlogAffiliateDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->affiliate)
                    ->setType(ActivityType::BLOG_AFFILIATE)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete blog affiliate: " . $this->affiliate->title);

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

    /** Only the columns of the affiliate; an empty value keeps the current one. */
    private function attributes(Request $request): array
    {
        return array_filter(
            $request->only(['title', 'bannerUrl', 'isAppliedToAll']),
            fn ($value, $key) => $value !== null || $key === 'bannerUrl' && $request->exists('bannerUrl'),
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function syncBannerImage(Request $request): void
    {
        $dirPath = PathConstant::IMAGES_BLOG_AFFILIATE_STORAGE_PUBLIC_PATH();

        if ($request->hasFile('bannerImage') && $request->file('bannerImage')->isValid()) {
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $this->removeBannerImage($dirPath);

            $image = $request->file('bannerImage');
            $filename = filename($image, $this->affiliate->title . '-affiliate');
            $image->move($dirPath, $filename);

            $this->affiliate->bannerImage = $filename;
            $this->affiliate->save();

        } elseif ($request->boolean('deleteBannerImage')) {
            $this->removeBannerImage($dirPath);

            $this->affiliate->bannerImage = null;
            $this->affiliate->save();
        }
    }

    private function removeBannerImage(string $dirPath): void
    {
        if ($this->affiliate->bannerImage && is_file($dirPath . $this->affiliate->bannerImage)) {
            unlink($dirPath . $this->affiliate->bannerImage);
        }
    }
}
