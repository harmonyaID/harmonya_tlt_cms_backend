<?php

namespace App\Services\WordPress;

use App\Models\Blog\Blog;
use App\Models\Blog\BlogCategory;
use App\Models\Blog\BlogTag;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WordPressBlogImporter
{
    protected string $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function fetchPage(int $page = 1, int $perPage = 20): array
    {
        $url = "{$this->baseUrl}/wp-json/wp/v2/posts";

        try {
            $response = Http::withOptions([
                'http_version' => CURL_HTTP_VERSION_1_1,
            ])
                ->connectTimeout(15)
                ->timeout(120)
                ->retry(3, 2000)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0',
                ])
                ->get($url, [
                    'page' => $page,
                    'per_page' => $perPage,
                    '_embed' => 1,
                ]);

            // WordPress returns 400 with
            // rest_post_invalid_page_number
            // once we're past the last page.
            if ($response->status() === 400) {
                return [];
            }

            if (!$response->successful()) {
                throw new \Exception(
                    "Unable to fetch WordPress posts (page {$page}). " .
                    "HTTP {$response->status()}: {$response->body()}"
                );
            }

            $data = $response->json();

            if (!is_array($data)) {
                throw new \Exception(
                    "WordPress returned an invalid JSON response " .
                    "for page {$page}."
                );
            }

            return $data;
        } catch (\Throwable $e) {
            throw new \Exception(
                "Failed to fetch WordPress page {$page}: {$e->getMessage()}",
                0,
                $e
            );
        }
    }

    public function importPost(array $post): Blog
    {
        $slug = $post['slug'];

        $title = html_entity_decode(
            strip_tags($post['title']['rendered'] ?? ''),
            ENT_QUOTES
        );

        $contentHtml = $this->fetchRenderedContent($post['link'] ?? null)
            ?? $this->cleanShortcodes(
                $post['content']['rendered'] ?? ''
            );

        $excerpt = html_entity_decode(
            strip_tags(
                $this->cleanShortcodes(
                    $post['excerpt']['rendered'] ?? ''
                )
            ),
            ENT_QUOTES
        );

        $excerpt = trim(
            preg_replace('/\s+/', ' ', $excerpt)
        );

        $categoryId = $this->resolveCategory($post);

        $tagIds = $this->resolveTags($post);

        $blog = Blog::updateOrCreate(
            ['slug' => $slug],
            [
                'categoryId' => $categoryId,
                'title' => $title,
                'excerpt' => Str::limit($excerpt, 500, ''),
                // 'content' => $contentHtml,
                'author' => $this->resolveAuthorName($post),
                'publishedAt' => $post['date'] ?? now(),
                'isActive' => ($post['status'] ?? 'publish') === 'publish',
            ]
        );

        $thumbnailUrl = $this->resolveFeaturedImageUrl($post);

        if ($thumbnailUrl && !$blog->thumbnail) {
            $filename = $this->downloadThumbnail(
                $thumbnailUrl,
                $slug
            );

            if ($filename) {
                $blog->thumbnail = $filename;
                $blog->save();
            }
        }

        if (!empty($tagIds)) {
            $blog->tags()->sync($tagIds);
        }

        return $blog;
    }

    private function fetchRenderedContent(?string $postUrl): ?string
    {
        if (!$postUrl) {
            return null;
        }

        try {
            $response = Http::withOptions([
                'http_version' => CURL_HTTP_VERSION_1_1,
            ])
                ->connectTimeout(10)
                ->timeout(60)
                ->retry(2, 1000)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0',
                ])
                ->get($postUrl);

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();

            libxml_use_internal_errors(true);

            $dom = new \DOMDocument();

            $dom->loadHTML(
                '<?xml encoding="UTF-8">' . $html
            );

            libxml_clear_errors();

            $xpath = new \DOMXPath($dom);

            $queries = [
                "//div[contains(concat(' ', normalize-space(@class), ' '), ' entry-content ')]",
                "//div[contains(concat(' ', normalize-space(@class), ' '), ' post-content ')]",
                "//article//div[contains(concat(' ', normalize-space(@class), ' '), ' content ')]",
                "//main//article",
            ];

            foreach ($queries as $query) {
                $nodes = $xpath->query($query);

                if ($nodes && $nodes->length > 0) {
                    $node = $nodes->item(0);

                    $innerHtml = $this->innerHtml($node);

                    if (
                        strlen(
                            trim(strip_tags($innerHtml))
                        ) > 100
                    ) {
                        return $this->cleanShortcodes($innerHtml);
                    }
                }
            }

            return null;
        } catch (\Throwable $e) {
            logger()->warning(
                "Failed to fetch rendered content from {$postUrl}: " .
                $e->getMessage()
            );

            return null;
        }
    }

    /**
     * @param \DOMNode $node
     */
    private function innerHtml(\DOMNode $node): string
    {
        $html = '';

        foreach ($node->childNodes as $child) {
            $html .= $node->ownerDocument->saveHTML($child);
        }

        return $html;
    }

    private function cleanShortcodes(string $content): string
    {
        $cleaned = preg_replace(
            '/\[(\/)?[a-zA-Z0-9_\-]+(?:\s[^\]]*)?\]/',
            '',
            $content
        );

        return trim($cleaned);
    }

    private function resolveCategory(array $post): ?int
    {
        $terms = $post['_embedded']['wp:term'][0] ?? [];

        $name = $terms[0]['name'] ?? null;

        if (!$name || $name === 'Uncategorized') {
            return null;
        }

        return BlogCategory::firstOrCreate([
            'name' => $name,
        ])->id;
    }

    private function resolveTags(array $post): array
    {
        $terms = $post['_embedded']['wp:term'][1] ?? [];

        $ids = [];

        foreach ($terms as $term) {
            if (empty($term['name'])) {
                continue;
            }

            $ids[] = BlogTag::firstOrCreate([
                'name' => $term['name'],
            ])->id;
        }

        return $ids;
    }

    private function resolveAuthorName(array $post): ?string
    {
        return $post['_embedded']['author'][0]['name'] ?? null;
    }

    private function resolveFeaturedImageUrl(array $post): ?string
    {
        return $post['_embedded']['wp:featuredmedia'][0]['source_url']
            ?? null;
    }

    private function downloadThumbnail(
        string $url,
        string $slug
    ): ?string {
        try {
            $response = Http::withOptions([
                'http_version' => CURL_HTTP_VERSION_1_1,
            ])
                ->connectTimeout(10)
                ->timeout(60)
                ->retry(2, 1000)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0',
                ])
                ->get($url);

            if (!$response->successful()) {
                return null;
            }

            $dirPath = PathConstant::IMAGES_BLOG_STORAGE_PUBLIC_PATH();

            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $extension = pathinfo(
                parse_url($url, PHP_URL_PATH),
                PATHINFO_EXTENSION
            ) ?: 'jpg';

            $filename = $slug . '-' . time() . '.' . $extension;

            file_put_contents(
                $dirPath . $filename,
                $response->body()
            );

            return $filename;
        } catch (\Throwable $e) {
            logger()->warning(
                "Failed to download WP thumbnail for {$slug}: " .
                $e->getMessage()
            );

            return null;
        }
    }
}