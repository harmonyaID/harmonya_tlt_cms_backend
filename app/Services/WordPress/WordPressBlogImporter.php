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

    public function fetchPage(int $page = 1, int $perPage = 5): array
    {
        $perPage = min(max($perPage, 1), 5);

        $response = Http::timeout(120)
            ->connectTimeout(20)
            ->get(
                "{$this->baseUrl}/wp-json/wp/v2/posts",
                [
                    'page' => $page,
                    'per_page' => $perPage,
                    '_embed' => 1,
                ]
            );

        if ($response->status() === 400) {
            return [];
        }

        if (!$response->successful()) {
            throw new \Exception(
                "Unable to fetch WordPress posts (page {$page}): " .
                $response->body()
            );
        }

        $data = $response->json();

        if (!is_array($data)) {
            throw new \Exception(
                "Invalid WordPress response on page {$page}"
            );
        }

        return $data;
    }

    public function importPost(array $post): Blog
    {
        $slug = $post['slug'] ?? null;

        if (!$slug) {
            throw new \Exception(
                'WordPress post does not have a slug'
            );
        }

        $title = html_entity_decode(
            strip_tags(
                $post['title']['rendered'] ?? ''
            ),
            ENT_QUOTES
        );

        $contentHtml = $this->fetchRenderedContent(
            $post['link'] ?? null
        ) ?? $this->cleanShortcodes(
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
            preg_replace(
                '/\s+/',
                ' ',
                $excerpt
            ) ?? ''
        );

        /*
         * WordPress categories can be multiple.
         */
        $categoryIds = $this->resolveCategories($post);

        /*
         * WordPress tags can also be multiple.
         */
        $tagIds = $this->resolveTags($post);

        $blog = Blog::updateOrCreate(
            [
                'slug' => $slug,
            ],
            [
                'title' => $title,

                'excerpt' => Str::limit(
                    $excerpt,
                    500,
                    ''
                ),

                'content' => $contentHtml,

                'author' => $this->resolveAuthorName(
                    $post
                ),

                'publishedAt' => $post['date'] ?? now(),

                'isActive' => (
                    $post['status'] ?? 'publish'
                ) === 'publish',
            ]
        );

        /*
         * Featured image.
         */
        $thumbnailUrl = $this->resolveFeaturedImageUrl(
            $post
        );

        if (
            $thumbnailUrl &&
            !$blog->thumbnail
        ) {
            $filename = $this->downloadThumbnail(
                $thumbnailUrl,
                $slug
            );

            if ($filename) {
                $blog->thumbnail = $filename;
                $blog->save();
            }
        }

        /*
         * Sync ALL categories.
         *
         * This will remove old relationships that no longer
         * exist in WordPress and insert the current categories.
         */
        $blog->categories()->sync(
            $categoryIds
        );

        /*
         * Sync ALL tags.
         */
        $blog->tags()->sync(
            $tagIds
        );

        return $blog;
    }

    private function fetchRenderedContent(
        ?string $postUrl
    ): ?string {
        if (!$postUrl) {
            return null;
        }

        try {
            $response = Http::timeout(60)
                ->connectTimeout(20)
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

                if (
                    $nodes &&
                    $nodes->length > 0
                ) {
                    $node = $nodes->item(0);

                    $innerHtml = $this->innerHtml(
                        $node
                    );

                    if (
                        strlen(
                            trim(
                                strip_tags(
                                    $innerHtml
                                )
                            )
                        ) > 100
                    ) {
                        return $this->cleanShortcodes(
                            $innerHtml
                        );
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

    private function innerHtml(
        \DOMNode $node
    ): string {
        $html = '';

        foreach ($node->childNodes as $child) {
            $html .= $node
                ->ownerDocument
                ->saveHTML($child);
        }

        return $html;
    }

    private function cleanShortcodes(
        string $content
    ): string {
        $cleaned = preg_replace(
            '/\[\/?[a-zA-Z0-9_-]+(?:\s[^\]]*)?\]/',
            '',
            $content
        );

        return trim(
            $cleaned ?? $content
        );
    }

    /**
     * Resolve ALL WordPress categories.
     *
     * WordPress REST API with _embed returns:
     *
     * _embedded
     *   wp:term
     *     0 = categories
     *     1 = tags
     */
    private function resolveCategories(
        array $post
    ): array {
        $terms = $post['_embedded']['wp:term'][0] ?? [];

        if (!is_array($terms)) {
            return [];
        }

        $categoryIds = [];

        foreach ($terms as $term) {
            $name = trim(
                $term['name'] ?? ''
            );

            if ($name === '') {
                continue;
            }

            /*
             * Don't import WordPress default category.
             */
            if (
                strcasecmp(
                    $name,
                    'Uncategorized'
                ) === 0
            ) {
                continue;
            }

            $category = BlogCategory::firstOrCreate(
                [
                    'name' => $name,
                ]
            );

            $categoryIds[] = $category->id;
        }

        return array_values(
            array_unique($categoryIds)
        );
    }

    /**
     * Resolve ALL WordPress tags.
     */
    private function resolveTags(
        array $post
    ): array {
        $terms = $post['_embedded']['wp:term'][1] ?? [];

        if (!is_array($terms)) {
            return [];
        }

        $tagIds = [];

        foreach ($terms as $term) {
            $name = trim(
                $term['name'] ?? ''
            );

            if ($name === '') {
                continue;
            }

            $tag = BlogTag::firstOrCreate(
                [
                    'name' => $name,
                ]
            );

            $tagIds[] = $tag->id;
        }

        return array_values(
            array_unique($tagIds)
        );
    }

    private function resolveAuthorName(
        array $post
    ): ?string {
        return $post['_embedded']['author'][0]['name']
            ?? null;
    }

    private function resolveFeaturedImageUrl(
        array $post
    ): ?string {
        return $post['_embedded']['wp:featuredmedia'][0]['source_url']
            ?? null;
    }

    private function downloadThumbnail(
        string $url,
        string $slug
    ): ?string {
        try {
            $response = Http::timeout(60)
                ->connectTimeout(20)
                ->get($url);

            if (!$response->successful()) {
                return null;
            }

            $dirPath = PathConstant::IMAGES_BLOG_STORAGE_PUBLIC_PATH();

            if (!file_exists($dirPath)) {
                mkdir(
                    $dirPath,
                    0777,
                    true
                );
            }

            $extension = pathinfo(
                parse_url(
                    $url,
                    PHP_URL_PATH
                ),
                PATHINFO_EXTENSION
            );

            $extension = $extension ?: 'jpg';

            $filename =
                $slug .
                '-' .
                time() .
                '.' .
                $extension;

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