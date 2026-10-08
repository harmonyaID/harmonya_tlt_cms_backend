<?php

namespace App\Console\Commands\Migration;

use App\Models\Blog\Blog;
use App\Models\Blog\BlogCategory;
use App\Models\Blog\BlogTag;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MigrateBlogsFromCsvCommand extends Command
{
    protected $signature = 'blogs:migrate-csv
        {--file= : CSV file path}
        {--max= : Stop after migrating this many posts}
        {--no-image : Do not download featured images}
        {--no-seo : Do not migrate SEO data}';

    protected $description = 'Migrate WordPress blog posts from CSV export';

    public function handle()
    {
        $file = $this->option('file');

        if (!$file) {
            $file = public_path(
                'migration/Posts-Export-2026-October-03-2029.csv'
            );
        }

        if (!file_exists($file)) {
            $this->error("CSV file not found: {$file}");

            return self::FAILURE;
        }

        $max = $this->option('max')
            ? (int) $this->option('max')
            : null;

        $noImage = (bool) $this->option('no-image');
        $noSeo = (bool) $this->option('no-seo');

        $this->info("Migrating blogs from:");
        $this->line($file);
        $this->newLine();

        $handle = fopen($file, 'r');

        if ($handle === false) {
            $this->error('Unable to open CSV file.');

            return self::FAILURE;
        }

        $headers = fgetcsv($handle);

        if (!$headers) {
            fclose($handle);

            $this->error('CSV header is empty.');

            return self::FAILURE;
        }

        $headers[0] = $this->removeBom($headers[0]);

        $imported = 0;
        $skipped = 0;
        $failed = 0;
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if ($max && $imported >= $max) {
                break;
            }

            if (count($row) !== count($headers)) {
                $this->warn(
                    "[{$rowNumber}] Invalid column count. " .
                        "Expected " . count($headers) .
                        ", got " . count($row)
                );

                $failed++;

                continue;
            }

            $data = array_combine($headers, $row);

            if (!$data) {
                $failed++;

                continue;
            }

            $postType = trim($data['Post Type'] ?? '');

            if ($postType !== 'post') {
                $skipped++;

                continue;
            }

            $slug = trim($data['Slug'] ?? '');

            if ($slug === '') {
                $this->warn(
                    "[{$rowNumber}] SKIP: slug is empty"
                );

                $skipped++;

                continue;
            }

            $title = html_entity_decode(
                trim($data['Title'] ?? ''),
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            );

            $this->line(
                "[{$rowNumber}] {$title}"
            );

            $this->line(
                "     slug: {$slug}"
            );

            $start = microtime(true);

            try {
                $blog = $this->migrateBlog($data);

                if (!$noImage) {
                    $this->migrateThumbnail(
                        $blog,
                        $data
                    );
                }

                $this->syncCategories(
                    $blog,
                    $data['Categories'] ?? ''
                );

                $this->syncTags(
                    $blog,
                    $data['Tags'] ?? ''
                );

                if (!$noSeo) {
                    $this->migrateSeo(
                        $blog,
                        $data
                    );
                }

                $imported++;

                $this->info(
                    "     OK ({$blog->id}) " .
                        round(microtime(true) - $start, 2) .
                        "s"
                );
            } catch (\Throwable $e) {
                $failed++;

                $this->error(
                    "     FAILED: {$slug}"
                );

                $this->error(
                    "     " . $e->getMessage()
                );
            }
        }

        fclose($handle);

        $this->newLine();
        $this->info('Migration completed.');
        $this->line("Imported : {$imported}");
        $this->line("Skipped  : {$skipped}");
        $this->line("Failed   : {$failed}");

        return $failed > 0
            ? self::FAILURE
            : self::SUCCESS;
    }

    private function migrateBlog(array $data): Blog
    {
        $slug = trim($data['Slug'] ?? '');

        $title = html_entity_decode(
            trim($data['Title'] ?? ''),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $content = $this->cleanContent(
            $data['Content'] ?? ''
        );

        $excerpt = trim(
            html_entity_decode(
                strip_tags(
                    $this->cleanContent(
                        $data['Excerpt'] ?? ''
                    )
                ),
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            )
        );

        $status = strtolower(
            trim($data['Status'] ?? 'draft')
        );

        $author = $this->resolveAuthor(
            $data
        );

        return Blog::updateOrCreate(
            [
                'slug' => $slug,
            ],
            [
                'title' => $title,

                'excerpt' => Str::limit(
                    preg_replace(
                        '/\s+/',
                        ' ',
                        $excerpt
                    ) ?? '',
                    500,
                    ''
                ),

                'content' => $content,

                'author' => $author,

                'publishedAt' => !empty($data['Date'])
                    ? $data['Date']
                    : now(),

                'isActive' => $status === 'publish',
            ]
        );
    }

    private function cleanContent(string $content): string
    {
        if ($content === '') {
            return '';
        }

        $content = html_entity_decode(
            $content,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        /*
         * Remove WordPress shortcodes.
         *
         * Examples:
         * [vc_row]
         * [/vc_row]
         * [vc_column width="1/2"]
         * [vc_empty_space height="20px"]
         * [envira-gallery id="123"]
         * [apss-share]
         */
        $content = preg_replace(
            '/\[\/?[a-zA-Z0-9_-]+(?:\s[^\]]*)?\]/',
            '',
            $content
        );

        /*
         * Remove WordPress comments.
         */
        $content = preg_replace(
            '/<!--.*?-->/s',
            '',
            $content
        );

        /*
         * Remove empty HTML elements generated by
         * Visual Composer.
         */
        $content = preg_replace(
            '/<p[^>]*>\s*<\/p>/i',
            '',
            $content
        );

        /*
         * Remove excessive whitespace.
         */
        $content = preg_replace(
            "/\r\n|\r/",
            "\n",
            $content
        );

        $content = preg_replace(
            "/\n{3,}/",
            "\n\n",
            $content
        );

        return trim($content);
    }

    private function syncCategories(
        Blog $blog,
        string $categories
    ): void {
        if (trim($categories) === '') {
            $blog->categories()->sync([]);

            return;
        }

        $categoryIds = [];

        foreach (
            explode('|', $categories)
            as $name
        ) {
            $name = html_entity_decode(
                trim($name),
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            );

            if ($name === '') {
                continue;
            }

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

        $blog->categories()->sync(
            array_values(
                array_unique($categoryIds)
            )
        );
    }

    private function syncTags(
        Blog $blog,
        string $tags
    ): void {
        if (trim($tags) === '') {
            $blog->tags()->sync([]);

            return;
        }

        $tagIds = [];

        foreach (
            explode('|', $tags)
            as $name
        ) {
            $name = html_entity_decode(
                trim($name),
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
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

        $blog->tags()->sync(
            array_values(
                array_unique($tagIds)
            )
        );
    }

    private function migrateThumbnail(
        Blog $blog,
        array $data
    ): void {
        if ($blog->thumbnail) {
            return;
        }

        $url = trim(
            $data['Image Featured']
                ?? $data['Image URL']
                ?? ''
        );

        if ($url === '') {
            return;
        }

        try {
            $contents = file_get_contents($url);

            if ($contents === false) {
                return;
            }

            $dirPath =
                PathConstant::IMAGES_BLOG_STORAGE_PUBLIC_PATH();

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
                $blog->slug .
                '-' .
                time() .
                '.' .
                $extension;

            file_put_contents(
                $dirPath . $filename,
                $contents
            );

            $blog->thumbnail = $filename;
            $blog->save();
        } catch (\Throwable $e) {
            logger()->warning(
                "Failed to download CSV blog thumbnail for {$blog->slug}: " .
                    $e->getMessage()
            );
        }
    }

    private function migrateSeo(
        Blog $blog,
        array $data
    ): void {
        $seoTitle = trim(
            $data['_yoast_wpseo_title'] ?? ''
        );

        $description = trim(
            $data['_yoast_wpseo_metadesc'] ?? ''
        );

        $canonical = trim(
            $data['_yoast_wpseo_canonical'] ?? ''
        );

        if (
            $seoTitle === '' &&
            $description === '' &&
            $canonical === ''
        ) {
            return;
        }

        $blog->seo()->updateOrCreate(
            [
                'contentableId' => $blog->id,
                'contentableType' => $blog->getMorphClass(),
            ],
            [
                'info' => null,

                'title' => $seoTitle !== ''
                    ? $seoTitle
                    : $blog->title,

                'slug' => $blog->slug,

                'description' => $description !== ''
                    ? $description
                    : null,

                'metaKeyword' => trim(
                    $data['_yoast_wpseo_focuskw'] ?? ''
                ) ?: null,

                'thumbnail' => null,

                'canonicalUrl' => $canonical !== ''
                    ? $canonical
                    : null,

                'robotIndex' => true,

                'robotFollow' => true,

                'schemaMarkup' => null,
            ]
        );
    }

    private function resolveAuthor(
        array $data
    ): ?string {
        $firstName = trim(
            $data['Author First Name'] ?? ''
        );

        $lastName = trim(
            $data['Author Last Name'] ?? ''
        );

        $name = trim(
            $firstName . ' ' . $lastName
        );

        if ($name !== '') {
            return $name;
        }

        return trim(
            $data['Author Username'] ?? ''
        ) ?: null;
    }

    private function removeBom(
        string $value
    ): string {
        return preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            $value
        );
    }
}
