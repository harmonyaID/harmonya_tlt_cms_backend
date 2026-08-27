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

    /**
     * Fetch one page of posts from the WordPress REST API.
     * `_embed=1` inlines featured media, author, and term (category/tag) data
     * so we don't need extra requests per post.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return array
     * @throws \Exception
     */
    public function fetchPage(int $page = 1, int $perPage = 20): array
    {
        $response = Http::timeout(30)->get("{$this->baseUrl}/wp-json/wp/v2/posts", [
            'page' => $page,
            'per_page' => $perPage,
            '_embed' => 1,
        ]);

        // WordPress returns 400 with code=rest_post_invalid_page_number once you're past the last page
        if ($response->status() === 400) {
            return [];
        }

        if (!$response->successful()) {
            throw new \Exception("Unable to fetch WordPress posts (page $page): " . $response->body());
        }

        return $response->json();
    }

    /**
     * Import (create or update, matched by slug) a single WordPress post into Blog.
     *
     * @param array $post raw WP REST API post object
     *
     * @return Blog
     */
    public function importPost(array $post): Blog
    {
        $slug = $post['slug'];
        $title = html_entity_decode(strip_tags($post['title']['rendered'] ?? ''), ENT_QUOTES);

        // Prefer the fully-rendered content from the live public page (guaranteed clean,
        // since that's what real visitors see and WPBakery renders shortcodes properly
        // there). Fall back to the REST API field (with shortcode-stripping) only if
        // the live page fetch fails or no matching content container is found.
        $contentHtml = $this->fetchRenderedContent($post['link'] ?? null)
            ?? $this->cleanShortcodes($post['content']['rendered'] ?? '');

        $excerpt = html_entity_decode(strip_tags($this->cleanShortcodes($post['excerpt']['rendered'] ?? '')), ENT_QUOTES);
        $excerpt = trim(preg_replace('/\s+/', ' ', $excerpt));

        $categoryId = $this->resolveCategory($post);
        $tagIds = $this->resolveTags($post);

        $blog = Blog::updateOrCreate(
            ['slug' => $slug],
            [
                'categoryId' => $categoryId,
                'title' => $title,
                'excerpt' => Str::limit($excerpt, 500, ''),
                'content' => $contentHtml,
                'author' => $this->resolveAuthorName($post),
                'publishedAt' => $post['date'] ?? now(),
                'isActive' => ($post['status'] ?? 'publish') === 'publish',
            ]
        );

        $thumbnailUrl = $this->resolveFeaturedImageUrl($post);
        if ($thumbnailUrl && !$blog->thumbnail) {
            $filename = $this->downloadThumbnail($thumbnailUrl, $slug);
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

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    /**
     * Fetch the live, fully-rendered post page and extract just the article
     * body HTML - bypassing the REST API entirely for this field, since some
     * page builders (WPBakery included) don't process their shortcodes when
     * WordPress serves content.rendered via the REST API.
     *
     * @param string|null $postUrl
     *
     * @return string|null
     */
    private function fetchRenderedContent(?string $postUrl): ?string
    {
        if (!$postUrl) {
            return null;
        }

        try {
            $response = Http::timeout(30)->get($postUrl);
            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();

            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
            libxml_clear_errors();

            $xpath = new \DOMXPath($dom);

            // Try common WordPress theme content-wrapper selectors, in priority order.
            // WPBakery renders its output *inside* one of these, as real HTML (vc_row/
            // vc_column divs etc.) - never as raw [bracket] shortcode text.
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

                    // Guard against matching an empty/near-empty wrapper
                    if (strlen(trim(strip_tags($innerHtml))) > 100) {
                        return $this->cleanShortcodes($innerHtml);
                    }
                }
            }

            return null;
        } catch (\Throwable $e) {
            logger()->warning("Failed to fetch rendered content from {$postUrl}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * @param \DOMNode $node
     *
     * @return string
     */
    private function innerHtml(\DOMNode $node): string
    {
        $html = '';
        foreach ($node->childNodes as $child) {
            $html .= $node->ownerDocument->saveHTML($child);
        }

        return $html;
    }

    /**
     * Strip page-builder shortcode wrappers (WPBakery/Visual Composer, e.g.
     * [vc_row][vc_column][vc_column_text]...) that sometimes leak through the
     * REST API unrendered. Only the bracket tags themselves are removed -
     * any real HTML (<p>, <strong>, <a href>, etc.) inside them is preserved.
     *
     * @param string $content
     *
     * @return string
     */
    private function cleanShortcodes(string $content): string
    {
        // Matches [shortcode_name attr="value" ...] and [/shortcode_name] forms.
        // Deliberately does NOT touch real HTML tags (those use < >, not [ ]).
        $cleaned = preg_replace('/\[\/?[a-zA-Z0-9_\-]+(?:\s[^\]]*)?\]/', '', $content);

        return trim($cleaned);
    }

    private function resolveCategory(array $post): ?int
    {
        $terms = $post['_embedded']['wp:term'][0] ?? [];
        $name = $terms[0]['name'] ?? null;

        // WordPress's default "Uncategorized" isn't worth creating a BlogCategory for
        if (!$name || $name === 'Uncategorized') {
            return null;
        }

        return BlogCategory::firstOrCreate(['name' => $name])->id;
    }

    private function resolveTags(array $post): array
    {
        $terms = $post['_embedded']['wp:term'][1] ?? [];

        $ids = [];
        foreach ($terms as $term) {
            if (empty($term['name'])) {
                continue;
            }
            $ids[] = BlogTag::firstOrCreate(['name' => $term['name']])->id;
        }

        return $ids;
    }

    private function resolveAuthorName(array $post): ?string
    {
        return $post['_embedded']['author'][0]['name'] ?? null;
    }

    private function resolveFeaturedImageUrl(array $post): ?string
    {
        return $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;
    }

    private function downloadThumbnail(string $url, string $slug): ?string
    {
        try {
            $response = Http::timeout(30)->get($url);
            if (!$response->successful()) {
                return null;
            }

            $dirPath = PathConstant::IMAGES_BLOG_STORAGE_PUBLIC_PATH();
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = $slug . '-' . time() . '.' . $extension;

            file_put_contents($dirPath . $filename, $response->body());

            return $filename;
        } catch (\Throwable $e) {
            logger()->warning("Failed to download WP thumbnail for {$slug}: " . $e->getMessage());
            return null;
        }
    }
}
