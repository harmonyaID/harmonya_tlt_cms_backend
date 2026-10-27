<?php

namespace App\Services\WordPress;

use App\Models\Boat\Boat;
use App\Models\Boat\BoatComponentType;
use App\Models\Boat\BoatPhoto;
use App\Models\SEO\ContentSeo;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Maps one raw row of the WordPress "Pages Export" into a Boat.
 *
 *   Boat Transfer : the boat company pages (Rocky, D'Stars, Setia, Bali Hai, D'Camel)
 *   Private Boat  : the private transfer pages (Morin, Rocky)
 *
 * Which Type a row belongs to comes from the "Type" column of the TLT
 * Migration List (MAP below, keyed by WordPress post ID):
 *
 *   "Boat - Boat Transfer"  -> Boat Type "Boat Transfer" (firstOrCreate)
 *   "Boat - Private Boat"   -> Boat Type "Private Boat"  (firstOrCreate)
 *
 * Rows that are not in MAP (booking forms, thank you pages, Stay, Experience,
 * Community ...) are not handled here - resolve() returns null for them. The name
 * is the page title (ALL CAPS titles are written in Title Case).
 *
 * What is filled in:
 *   name, description (clean HTML), isActive, SEO (slug, title, description)
 *   schedule            "FROM SANUR, BALI - 9.15 AM, 1 PM"  /  timetable table  /  Bali Hai list
 *   customInformations  price tables ("OPTION 1 - ..."), "Boat Profile", private "Price:" lists
 *   discountLabel       "With 20% OFF" -> "20% OFF"
 *   priceFiles          the "VIEW PRICE LIST" PDF
 *   photos / promoPhotos  Image URL column ("banner" images are promo photos)
 *
 * Safe to re-run: an existing Boat is matched by its WordPress slug (its SEO slug)
 * and updated; photos / files are only added when missing.
 */
class WordPressBoatImporter
{
    /** Files (photos / price list PDFs) that were not found in the image folder (file names). */
    public array $missingThumbnails = [];

    /** files that could not be put in place: file name => reason */
    public array $fileErrors = [];

    private array $types = [];

    public function __construct(private WordPressContentConverter $converter)
    {
    }

    /**
     * WordPress post ID => "Type" column of the TLT Migration List.
     */
    private const MAP = [
        49319 => 'Boat - Boat Transfer', // ROCKY FAST CRUISES
        50897 => 'Boat - Boat Transfer', // D'STARS FAST FERRY
        67107 => 'Boat - Boat Transfer', // SETIA FAST FERRY
        74241 => 'Boat - Boat Transfer', // BALI HAI CRUISES
        76010 => 'Boat - Boat Transfer', // D'CAMEL FAST FERRY
        49328 => 'Boat - Private Boat', // PRIVATE TRANSFERS - MORIN
        68022 => 'Boat - Private Boat', // ROCKY FAST CRUISE PRIVATE TRANSFERS
    ];

    /** @param array<int|string,string> $idToUrl WordPress media ID => URL, for images inside descriptions */
    public function setMedia(array $idToUrl): void
    {
        $this->converter->setMedia($idToUrl);
    }

    /**
     * ['type' => 'Boat Transfer', 'name' => "D'Stars Fast Ferry"] for a Boat row,
     * null for every other row of the export.
     */
    public function resolve(array $row): ?array
    {
        $label = self::MAP[(int) ($row['ID'] ?? 0)] ?? null;

        if (!$label) {
            return null;
        }

        [, $type] = array_pad(array_map('trim', explode(' - ', $label, 2)), 2, null);

        $title = $this->decode($row['Title'] ?? null) ?? '';

        if (!$type || $title === '' || ($row['Status'] ?? null) !== 'publish') {
            return null;
        }

        return ['type' => $type, 'name' => $this->titleName($title)];
    }

    /** "D'STARS FAST FERRY" => "D'Stars Fast Ferry"; mixed case titles are kept */
    private function titleName(string $title): string
    {
        if ($title !== mb_strtoupper($title)) {
            return $title;
        }

        $name = mb_strtolower($title);

        return preg_replace_callback("/(^|[\s'’\-(])(\p{L})/u", fn ($m) => $m[1] . mb_strtoupper($m[2]), $name);
    }

    public function import(array $row, ?string $imageDir = null, bool $withDescription = true, bool $download = false): Boat
    {
        $target = $this->resolve($row);

        if (!$target) {
            throw new \InvalidArgumentException('Row ' . ($row['ID'] ?? '?') . ' is not a Boat in the migration list.');
        }

        return DB::transaction(function () use ($row, $target, $imageDir, $withDescription, $download) {
            $type = $this->types[$target['type']] ??= BoatComponentType::firstOrCreate(['name' => $target['type']]);

            $name = $target['name'];
            $slug = trim($row['Slug'] ?? '') ?: Str::slug($name);
            $content = (string) ($row['Content'] ?? '');

            // price tables first: they become custom informations
            [$content, $groups, $discount] = $this->extractServiceTables($content);
            $groups = array_merge($groups, $this->parseBoatProfile($content), $this->parsePriceLists($content), $this->parseLabelledPrices($content));

            // the book button is not part of the description
            $content = preg_replace('/\[qbutton\b[^\]]*\]/i', '', $content);

            $attributes = [
                'boatComponentTypeId' => $type->id,
                'name' => $name,
                'isActive' => ($row['Status'] ?? null) === 'publish',
                'schedule' => $this->parseSchedule($content) ?: null,
                'discountLabel' => $discount,
            ];

            if ($withDescription) {
                $attributes['description'] = $this->converter->convert($content, $name);
            }

            $seoId = ContentSeo::where('slug', $slug)
                ->where('contentableType', Boat::class)
                ->value('contentableId');

            $boat = $seoId ? Boat::find($seoId) : null;

            if ($boat) {
                $boat->update($attributes);
            } else {
                $boat = Boat::create($attributes + [
                    'locale' => 'en',
                    'createdAt' => $this->date($row['Date'] ?? null),
                    'updatedAt' => $this->date($row['Post Modified Date'] ?? null),
                ]);
            }

            $this->syncCustomInformations($boat, $groups);
            $this->syncPhotos($boat, $row, $imageDir, $download);
            $this->syncPriceFiles($boat, (string) ($row['Content'] ?? ''), $imageDir, $download);
            $this->syncSeo($boat, $slug, $name, $row);

            return $boat;
        });
    }

    /* ------------------------------------------------------------------ *
     |  Schedule
     * ------------------------------------------------------------------ */

    /**
     * @return array<int,array{from:string,to:string,times:string[]}>
     */
    private function parseSchedule(string $content): array
    {
        return $this->scheduleFromFromLines($content)
            ?: $this->scheduleFromTable($content)
            ?: $this->scheduleFromArrowLists($content);
    }

    /** "FROM SANUR, BALI - 9.15 AM, 1 PM, 5 PM" + "FROM JUNGUTBATU - 8 AM, 12 PM" */
    private function scheduleFromFromLines(string $content): array
    {
        $text = html_entity_decode(strip_tags(preg_replace('/\[[^\]]*\]/', '', $content)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $time = '\d{1,2}(?:[.:]\d{2})?\s*(?:AM|PM)';

        if (!preg_match_all('/FROM\s+(.+?)\s*[–—-]\s*(' . $time . '(?:\s*,\s*' . $time . ')*)/iu', $text, $matches, PREG_SET_ORDER)) {
            return [];
        }

        $legs = [];
        foreach ($matches as $m) {
            $legs[] = [
                'from' => $this->placeName($m[1]),
                'times' => array_values(array_filter(array_map(fn ($t) => $this->timeLabel($t), explode(',', $m[2])))),
            ];
        }

        // the destination of a leg is the starting point of the other one
        $schedule = [];
        foreach ($legs as $i => $leg) {
            $other = $legs[$i === 0 ? 1 : 0] ?? null;

            if (!$other || !$leg['times']) {
                continue;
            }

            $schedule[] = ['from' => $leg['from'], 'to' => $other['from'], 'times' => $leg['times']];
        }

        return $schedule;
    }

    /** table: Departure | Destination | ETD | ETA */
    private function scheduleFromTable(string $content): array
    {
        if (!preg_match('#<table\b.*?</table>#is', $content, $table)) {
            return [];
        }

        $schedule = [];
        preg_match_all('#<tr\b.*?</tr>#is', $table[0], $rows);

        foreach ($rows[0] as $row) {
            if (stripos($row, '<th') !== false) {
                continue;
            }

            preg_match_all('#<td\b[^>]*>(.*?)</td>#is', $row, $cells);
            $cells = array_map(fn ($c) => trim(html_entity_decode(strip_tags($c))), $cells[1]);

            if (count($cells) >= 3 && $cells[0] !== '' && $cells[1] !== '' && $cells[2] !== '') {
                $schedule[] = ['from' => $cells[0], 'to' => $cells[1], 'times' => [$this->timeLabel($cells[2])]];
            }
        }

        return $schedule;
    }

    /** <strong>Bali → Nusa Lembongan</strong> <strong>09:00</strong> - Departure from ... */
    private function scheduleFromArrowLists(string $content): array
    {
        $pattern = '#<strong>\s*([^<]+?)\s*(?:→|➝|➔|->|&rarr;)\s*([^<]+?)\s*</strong>\s*(?:<strong>\s*(\d{1,2}[:.]\d{2})\s*</strong>)#iu';

        if (!preg_match_all($pattern, html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8'), $matches, PREG_SET_ORDER)) {
            return [];
        }

        return array_map(fn ($m) => [
            'from' => trim($m[1]),
            'to' => trim($m[2]),
            'times' => [$this->timeLabel($m[3])],
        ], $matches);
    }

    private function placeName(string $name): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $name));
        $name = mb_convert_case(mb_strtolower($name), MB_CASE_TITLE);

        return str_replace([' Of ', ' The ', ' And ', ' At '], [' of ', ' the ', ' and ', ' at '], $name);
    }

    private function timeLabel(string $time): string
    {
        return trim(preg_replace('/\s+/', ' ', $time));
    }

    /* ------------------------------------------------------------------ *
     |  Custom informations
     * ------------------------------------------------------------------ */

    /**
     * [service_table title="OPTION 1 - ..."]<ul><li><strong>RETURN</strong> Adult: AU$65 ...</li></ul>[/service_table]
     *
     * @return array{0:string,1:array,2:?string} content without the tables, groups, discount label
     */
    private function extractServiceTables(string $content): array
    {
        $groups = [];
        $discount = null;

        $content = preg_replace_callback(
            '#\[service_table\b([^\]]*)\](.*?)\[/service_table\]#is',
            function ($m) use (&$groups, &$discount) {
                $title = preg_match('/\btitle="([^"]*)"/', $m[1], $t) ? $this->clean($t[1]) : 'Price';
                $items = [];

                preg_match_all('#<li\b[^>]*>(.*?)</li>#is', $m[2], $lis);

                foreach ($lis[1] as $li) {
                    $name = 'Note';
                    $rest = $li;

                    if (preg_match('#^\s*<(strong|b)>(.*?)</\1>#is', $li, $h)) {
                        $name = $this->clean($h[2]) ?: 'Note';
                        $rest = substr($li, strlen($h[0]));
                    }

                    // one <li> can hold several headings (RETURN ... ONE WAY ...)
                    $blocks = [[$name, []]];
                    foreach ($this->lines($rest) as $line) {
                        if (preg_match('/^(return|one[\s-]?way|round trip)$/i', $line)) {
                            $blocks[] = [strtoupper($line), []];
                        } else {
                            $blocks[count($blocks) - 1][1][] = $line;
                        }
                    }

                    foreach ($blocks as [$blockName, $lines]) {
                        $main = [];
                        foreach ($lines as $line) {
                            if (preg_match('/^with\s+(\d+\s*%\s*off)\s*[:\-]?\s*(.*)$/i', $line, $d)) {
                                $label = strtoupper(preg_replace('/\s+/', '', $d[1]));
                                $label = preg_replace('/(\d+%)(OFF)/', '$1 $2', $label);
                                $discount ??= $label;
                                $items[] = ['name' => "{$blockName} ({$label})", 'value' => $d[2] !== '' ? $d[2] : $label];
                            } else {
                                $main[] = $line;
                            }
                        }

                        if ($main) {
                            if ($blockName === 'Note') {
                                $main = [trim(preg_replace('/^note\s*:\s*/i', '', implode(' ', $main)))];
                            }

                            $items[] = ['name' => $blockName, 'value' => implode("\n", $main)];
                        }
                    }
                }

                if ($items) {
                    $groups[] = ['name' => $title, 'items' => $items];
                }

                return '';
            },
            $content
        );

        return [$content, $groups, $discount];
    }

    /** "Boat Profile:" <ul>...</ul> (private boats) */
    private function parseBoatProfile(string $content): array
    {
        if (!preg_match('#Boat\s+Profile\s*(?:</strong>)?\s*:?\s*(?:</strong>)?\s*<ul>(.*?)</ul>#is', $content, $m)) {
            return [];
        }

        preg_match_all('#<li\b[^>]*>(.*?)</li>#is', $m[1], $lis);

        $items = [];
        foreach ($lis[1] as $li) {
            $text = $this->clean($li);
            if ($text === '') {
                continue;
            }

            if (preg_match('/^([^:]{2,40})\s*:\s*(.+)$/u', $text, $kv)) {
                $items[] = ['name' => trim($kv[1]), 'value' => trim($kv[2])];
            } else {
                $items[] = ['name' => 'Feature', 'value' => $text];
            }
        }

        return $items ? [['name' => 'Boat Profile', 'items' => $items]] : [];
    }

    /**
     * <strong>Price:</strong><ul><li>IDR 7.000,000 for one way</li></ul> (private boats).
     * The group is named after the nearest heading above (e.g. "Sanur to Lembongan"),
     * "Capacity" / "Duration" lines right below are added to the same group.
     */
    private function parsePriceLists(string $content): array
    {
        $ignore = ['price', 'capacity', 'duration', 'private transfers', 'terms & conditions', 'term & conditions',
            'information to provide upon booking', 'boat profile', 'please note', 'departure time'];

        $groups = [];

        if (!preg_match_all('#<strong>\s*Price\s*:?\s*</strong>\s*:?\s*<ul>(.*?)</ul>#is', $content, $prices, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return [];
        }

        foreach ($prices as $price) {
            $offset = $price[0][1];
            $before = substr($content, 0, $offset);

            $title = 'Price';
            if (preg_match_all('#<strong>\s*([^<]+?)\s*</strong>#i', $before, $headings)) {
                foreach (array_reverse($headings[1]) as $heading) {
                    $heading = trim(html_entity_decode($heading, ENT_QUOTES | ENT_HTML5, 'UTF-8'), " :\xC2\xA0");
                    if ($heading !== '' && !in_array(strtolower($heading), $ignore, true)) {
                        $title = $heading;
                        break;
                    }
                }
            }

            preg_match_all('#<li\b[^>]*>(.*?)</li>#is', $price[1][0], $lis);

            $items = [];
            foreach ($lis[1] as $li) {
                $text = $this->clean($li);
                if ($text === '') {
                    continue;
                }

                if (preg_match('/^((?:IDR|Rp\.?|AU\$|US\$|USD|AUD)\s*[\d.,]+)\s*(?:for\s+(.+))?$/iu', $text, $p)) {
                    $items[] = ['name' => ucfirst(trim($p[2] ?? '')) ?: 'Price', 'value' => trim($p[1])];
                } else {
                    $items[] = ['name' => 'Price', 'value' => $text];
                }
            }

            // Capacity / Duration right below the price list
            $after = substr($content, $offset + strlen($price[0][0]), 700);
            // stop at the next heading that is not Capacity / Duration
            if (preg_match('#<strong>\s*(?!Capacity|Duration)[^<]+</strong>#i', $after, $stop, PREG_OFFSET_CAPTURE)) {
                $after = substr($after, 0, $stop[0][1]);
            }
            if (preg_match_all('#<strong>\s*(Capacity|Duration)\s*:?\s*</strong>\s*:?\s*([^<\r\n]+)#i', $after, $extra, PREG_SET_ORDER)) {
                foreach ($extra as $e) {
                    $items[] = ['name' => ucfirst(strtolower($e[1])), 'value' => $this->clean($e[2])];
                }
            }

            if ($items) {
                $groups[] = ['name' => $title, 'items' => $items];
            }
        }

        return $groups;
    }

    /** "Adult (above 14 years old): IDR 700,000" lines under a "One Way Price" heading */
    private function parseLabelledPrices(string $content): array
    {
        if (!preg_match('#<strong>\s*([^<]*price[^<]*)</strong>(.*?)(?=<strong>|$)#is', $content, $m)) {
            return [];
        }

        $items = [];
        foreach ($this->lines($m[2]) as $line) {
            if (preg_match('/^(Adults?|Child(?:ren)?|Infants?)\s*(?:\(([^)]*)\))?\s*:\s*(.+)$/iu', $line, $p)) {
                $items[] = ['name' => trim($p[1] . ($p[2] !== '' ? " ({$p[2]})" : '')), 'value' => trim($p[3])];
            }
        }

        return $items ? [['name' => $this->clean($m[1]) ?: 'Price', 'items' => $items]] : [];
    }

    private function syncCustomInformations(Boat $boat, array $groups): void
    {
        $boat->customInformations()->delete();

        $order = 0;
        foreach ($groups as $group) {
            foreach ($group['items'] as $item) {
                $boat->customInformations()->create([
                    'groupName' => $group['name'],
                    'name' => Str::limit($item['name'], 250, ''),
                    'value' => Str::limit($item['value'], 250, ''),
                    'order' => $order++,
                ]);
            }
        }
    }

    /* ------------------------------------------------------------------ *
     |  Photos & files
     * ------------------------------------------------------------------ */

    private function syncPhotos(Boat $boat, array $row, ?string $imageDir, bool $download): void
    {
        $urls = array_values(array_filter(array_map('trim', explode('|', (string) ($row['Image URL'] ?? '')))));
        $featured = trim((string) ($row['Image Featured'] ?? ''));

        if ($featured !== '') {
            $urls = array_values(array_unique(array_merge([$featured], $urls)));
        }

        $photoDir = PathConstant::IMAGES_BOAT_STORAGE_PUBLIC_PATH();
        $promoDir = PathConstant::IMAGES_BOAT_PROMO_STORAGE_PUBLIC_PATH();

        $existing = $boat->photos()->pluck('photo')->all();
        $promo = $boat->promoPhotos ?? [];
        $promoFiles = array_map(fn ($p) => is_array($p) ? ($p['file'] ?? null) : $p, $promo);
        $order = (int) $boat->photos()->max('order');

        foreach ($urls as $url) {
            $file = $this->fileName($url);
            if (!$file || !preg_match('#^https?://#i', $url)) {
                continue;
            }

            if (stripos($file, 'banner') !== false) {
                if (in_array($file, $promoFiles, true)) {
                    continue;
                }

                if ($this->placeFile($promoDir, $file, $url, $imageDir, $download)) {
                    $promo[] = ['id' => $promo ? max(array_column($promo, 'id')) + 1 : 1, 'file' => $file];
                    $promoFiles[] = $file;
                }

                continue;
            }

            if (in_array($file, $existing, true)) {
                continue;
            }

            if ($this->placeFile($photoDir, $file, $url, $imageDir, $download)) {
                BoatPhoto::create(['boatId' => $boat->id, 'photo' => $file, 'order' => ++$order]);
                $existing[] = $file;
            }
        }

        if ($promo !== ($boat->promoPhotos ?? [])) {
            $boat->update(['promoPhotos' => $promo]);
        }
    }

    /** the "VIEW PRICE LIST" PDF(s) linked in the page */
    private function syncPriceFiles(Boat $boat, string $content, ?string $imageDir, bool $download): void
    {
        if (!preg_match_all('#href="(https?://[^"]+\.pdf)"#i', $content, $m)) {
            return;
        }

        $dir = PathConstant::FILES_BOAT_STORAGE_PUBLIC_PATH();
        $files = $boat->priceFiles ?? [];
        $names = array_map(fn ($f) => is_array($f) ? ($f['file'] ?? null) : $f, $files);

        foreach (array_unique($m[1]) as $url) {
            $file = $this->fileName($url);

            if (!$file || in_array($file, $names, true)) {
                continue;
            }

            if ($this->placeFile($dir, $file, $url, $imageDir, $download)) {
                $files[] = ['id' => $files ? max(array_column($files, 'id')) + 1 : 1, 'file' => $file];
                $names[] = $file;
            }
        }

        if ($files !== ($boat->priceFiles ?? [])) {
            $boat->update(['priceFiles' => $files]);
        }
    }

    /**
     * Makes sure $dir/$filename exists: already there, copied from $imageDir
     * or downloaded from its WordPress URL.
     */
    private function placeFile(string $dir, string $filename, string $url, ?string $imageDir, bool $download): bool
    {
        if (is_file($dir . $filename)) {
            return true;
        }

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        if ($imageDir) {
            $source = rtrim($imageDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

            if (is_file($source)) {
                return copy($source, $dir . $filename);
            }
        }

        if ($download) {
            try {
                $response = Http::timeout(30)
                    ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36')
                    ->get($url);

                if ($response->successful() && $response->body() !== '') {
                    return file_put_contents($dir . $filename, $response->body()) !== false;
                }

                $this->fileErrors[$filename] = 'HTTP ' . $response->status();
            } catch (\Throwable $e) {
                $this->fileErrors[$filename] = Str::limit($e->getMessage(), 90);
            }
        } else {
            $this->fileErrors[$filename] = 'not in the image folder (and --download-images not used)';
        }

        $this->missingThumbnails[] = $filename;

        return false;
    }

    /* ------------------------------------------------------------------ *
     |  SEO & helpers
     * ------------------------------------------------------------------ */

    private function syncSeo(Boat $boat, string $slug, string $name, array $row): void
    {
        $seoTitle = trim($row['_yoast_wpseo_title'] ?? '');

        $boat->seo()->updateOrCreate(
            ['contentableId' => $boat->id, 'contentableType' => Boat::class],
            [
                'title' => $this->decode($seoTitle !== '' && !str_contains($seoTitle, '%%') ? $seoTitle : $name),
                'slug' => $slug,
                'description' => $this->decode($row['_yoast_wpseo_metadesc'] ?? null),
                'robotIndex' => true,
                'robotFollow' => true,
            ]
        );
    }

    private function fileName(string $url): ?string
    {
        $path = parse_url(trim($url), PHP_URL_PATH);

        return $path ? (rawurldecode(basename($path)) ?: null) : null;
    }

    /** text of an HTML fragment on one line */
    private function clean(?string $html): string
    {
        $text = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/[\s\xC2\xA0]+/u', ' ', $text));
    }

    /** non-empty text lines of an HTML fragment (a line break = new line) */
    private function lines(string $html): array
    {
        $html = preg_replace('#<br\s*/?>#i', "\n", $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return array_values(array_filter(array_map(
            fn ($l) => trim(preg_replace('/[ \t\xC2\xA0]+/u', ' ', $l)),
            preg_split('/\R/u', $text)
        ), fn ($l) => $l !== ''));
    }

    private function decode(?string $value): ?string
    {
        $value = trim(html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return $value === '' ? null : $value;
    }

    private function date(?string $value): ?string
    {
        $value = trim((string) $value);

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value . ' 00:00:00' : null;
    }
}