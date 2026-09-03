<?php

namespace App\Algorithms\Seo;

use App\Models\SEO\ContentSeo;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;

class ContentSeoAlgo
{
    public function __construct(
        protected mixed $contentable
    ) {
    }

    public function save(Request $request): ContentSeo
    {
        $seo = $request->input('seo', []);

        $contentSeo = $this->contentable->seo()->firstOrNew([]);

        $contentSeo->fill([
            'info' => $seo['info'] ?? null,
            'title' => $seo['title'] ?? null,
            'slug' => $seo['slug'] ?? null,
            'description' => $seo['description'] ?? null,
            'metaKeyword' => $seo['metaKeyword'] ?? null,
            'canonicalUrl' => $seo['canonicalUrl'] ?? null,
            'robotIndex' => $seo['robotIndex'] ?? true,
            'robotFollow' => $seo['robotFollow'] ?? true,
            'schemaMarkup' => $seo['schemaMarkup'] ?? null,
        ]);

        if ($request->boolean('seo.deleteThumbnail')) {
            $this->deleteThumbnail($contentSeo);
            $contentSeo->thumbnail = null;
        }

        if ($request->hasFile('seo.thumbnail') && $request->file('seo.thumbnail')->isValid()) {
            $contentSeo->thumbnail = $this->uploadThumbnail($request, $contentSeo);
        }

        $this->contentable->seo()->save($contentSeo);

        return $contentSeo->refresh();
    }

    /*
    |--------------------------------------------------------------------------
    | Functions
    |--------------------------------------------------------------------------
    */

    private function uploadThumbnail(Request $request, ContentSeo $contentSeo): string
    {
        $image = $request->file('seo.thumbnail');

        $dirPath = PathConstant::IMAGES_SEO_STORAGE_PUBLIC_PATH();

        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        if ($contentSeo->thumbnail && file_exists($dirPath . $contentSeo->thumbnail)) {
            unlink($dirPath . $contentSeo->thumbnail);
        }

        $filename = filename(
            $image,
            $request->input('seo.title')
                ?: $request->input('seo.slug')
                ?: class_basename($this->contentable)
        );

        $image->move($dirPath, $filename);

        return $filename;
    }

    private function deleteThumbnail(ContentSeo $contentSeo): void
    {
        $dirPath = PathConstant::IMAGES_SEO_STORAGE_PUBLIC_PATH();

        if ($contentSeo->thumbnail && file_exists($dirPath . $contentSeo->thumbnail)) {
            unlink($dirPath . $contentSeo->thumbnail);
        }
    }
}