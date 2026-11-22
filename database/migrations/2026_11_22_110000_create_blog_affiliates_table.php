<?php

use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Blog affiliates replace the blog promo banner.
 *
 * - isAppliedToAll = true:  shown on every blog
 * - isAppliedToAll = false: shown only on the blogs it is assigned to (blog_affiliate)
 *
 * The promo banner of existing blogs becomes an affiliate assigned to that blog.
 * blogs.promoBanner / promoBannerUrl are kept (no longer used by the API).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('blog_affiliates')) {
            Schema::create('blog_affiliates', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('bannerImage')->nullable();
                $table->string('bannerUrl', 2048)->nullable();
                $table->boolean('isAppliedToAll')->default(false)->index();
                $table->timestamp('createdAt')->nullable();
                $table->timestamp('updatedAt')->nullable();
                $table->timestamp('deletedAt')->nullable();
            });
        }

        if (!Schema::hasTable('blog_affiliate')) {
            Schema::create('blog_affiliate', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('blogId')->index();
                $table->unsignedBigInteger('affiliateId')->index();
                $table->unique(['blogId', 'affiliateId']);
            });
        }

        $this->convertPromoBanners();
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_affiliate');
        Schema::dropIfExists('blog_affiliates');
    }

    private function convertPromoBanners(): void
    {
        if (!Schema::hasColumn('blogs', 'promoBanner')) {
            return;
        }

        $from = PathConstant::IMAGES_BLOG_STORAGE_PUBLIC_PATH();
        $to = PathConstant::IMAGES_BLOG_AFFILIATE_STORAGE_PUBLIC_PATH();

        DB::table('blogs')
            ->where(fn ($q) => $q->whereNotNull('promoBanner')->orWhereNotNull('promoBannerUrl'))
            ->orderBy('id')
            ->each(function ($blog) use ($from, $to) {
                $title = mb_substr(trim(($blog->title ?: 'Blog #' . $blog->id) . ' - Promo Banner'), 0, 255);

                // already converted (migration run again)
                if (DB::table('blog_affiliate')->join('blog_affiliates', 'blog_affiliates.id', '=', 'blog_affiliate.affiliateId')
                    ->where('blog_affiliate.blogId', $blog->id)->where('blog_affiliates.title', $title)->exists()) {
                    return;
                }

                $image = null;
                if ($blog->promoBanner && is_file($from . $blog->promoBanner)) {
                    if (!is_dir($to)) {
                        mkdir($to, 0777, true);
                    }
                    $image = $blog->promoBanner;
                    copy($from . $blog->promoBanner, $to . $image);
                }

                $affiliateId = DB::table('blog_affiliates')->insertGetId([
                    'title' => $title,
                    'bannerImage' => $image,
                    'bannerUrl' => $blog->promoBannerUrl,
                    'isAppliedToAll' => false,
                    'createdAt' => now(),
                    'updatedAt' => now(),
                ]);

                DB::table('blog_affiliate')->insert(['blogId' => $blog->id, 'affiliateId' => $affiliateId]);
            });
    }
};
