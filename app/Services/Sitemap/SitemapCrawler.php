<?php

namespace App\Services\Sitemap;

use DOMDocument;
use DOMXPath;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Crawl website (frontend) mulai dari satu URL, ikuti link internal, lalu
 * hasilnya ditulis menjadi sitemap.xml.
 *
 * Karena yang dibaca adalah halaman yang benar-benar tayang, halaman draft /
 * dihapus / noindex otomatis tidak ikut begitu crawl diulang.
 *
 * Catatan: crawler membaca HTML dari server. Halaman yang link-nya baru muncul
 * lewat JavaScript di browser (client-side rendering murni) tidak akan terbaca.
 */
class SitemapCrawler
{
    const FILE_PATH = 'sitemap/sitemap.xml';
    const DISK = 'public';

    private const SKIP_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'avif', 'pdf', 'zip', 'rar', 'mp4', 'mp3', 'webm',
        'css', 'js', 'json', 'xml', 'txt', 'woff', 'woff2', 'ttf', 'eot', 'map', 'doc', 'docx', 'xls', 'xlsx',
    ];

    private string $scheme;
    private string $host;

    public function __construct(
        private string $baseUrl,
        private int $maxPages = 300,
        private int $maxDepth = 6,
        private int $batchSize = 8,
    ) {
        $parts = parse_url(trim($baseUrl));
        if (empty($parts['host'])) {
            throw new \InvalidArgumentException('Invalid website url');
        }

        $this->scheme = $parts['scheme'] ?? 'https';
        $this->host = strtolower($parts['host']);
    }

    /**
     * @return array{urls: array, scanned: int, skipped: int, file: string}
     */
    public function run(): array
    {
        $start = $this->normalize($this->baseUrl, $this->scheme . '://' . $this->host . '/');

        $queue = [[$start, 0]];
        $seen = [$start => true];
        $found = [];
        $scanned = 0;
        $skipped = 0;

        while ($queue && $scanned < $this->maxPages) {
            $batch = array_splice($queue, 0, min($this->batchSize, $this->maxPages - $scanned));

            $responses = Http::pool(fn($pool) => array_map(
                fn($item) => $pool->as($item[0])
                    ->withHeaders(['User-Agent' => 'SitemapCrawler/1.0', 'Accept' => 'text/html'])
                    ->timeout(15)
                    ->get($item[0]),
                $batch
            ));

            foreach ($batch as [$url, $depth]) {
                $scanned++;
                $response = $responses[$url] ?? null;

                if (!$response instanceof Response || !$response->successful() || !$this->isHtml($response)) {
                    $skipped++;
                    continue;
                }

                $dom = $this->parse($response->body());
                if (!$dom) {
                    $skipped++;
                    continue;
                }

                $xpath = new DOMXPath($dom);

                // link tetap diikuti walau halaman ini noindex, tapi halamannya tidak dimasukkan
                if ($this->isNoindex($xpath)) {
                    $skipped++;
                } else {
                    $found[$this->canonical($xpath, $url)] = $this->lastModified($response);
                }

                if ($depth >= $this->maxDepth) {
                    continue;
                }

                foreach ($xpath->query('//a[@href]') as $a) {
                    $next = $this->resolve($a->getAttribute('href'), $url);
                    if ($next && !isset($seen[$next])) {
                        $seen[$next] = true;
                        $queue[] = [$next, $depth + 1];
                    }
                }
            }
        }

        $urls = array_keys($found);
        sort($urls);

        Storage::disk(self::DISK)->put(self::FILE_PATH, $this->buildXml($found));

        return [
            'urls' => $urls,
            'scanned' => $scanned,
            'skipped' => $skipped,
            'file' => self::FILE_PATH,
        ];
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    private function isHtml(Response $response): bool
    {
        return str_contains(strtolower($response->header('Content-Type')), 'text/html');
    }

    private function parse(string $html): ?DOMDocument
    {
        if (trim($html) === '') {
            return null;
        }

        $dom = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $loaded ? $dom : null;
    }

    private function isNoindex(DOMXPath $xpath): bool
    {
        foreach ($xpath->query('//meta[translate(@name,"ROBOTS","robots")="robots"]/@content') as $content) {
            if (str_contains(strtolower($content->nodeValue), 'noindex')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Pakai <link rel="canonical"> bila ada dan masih di host yang sama.
     */
    private function canonical(DOMXPath $xpath, string $url): string
    {
        $node = $xpath->query('//link[@rel="canonical"]/@href')->item(0);
        if ($node) {
            $canonical = $this->resolve($node->nodeValue, $url);
            if ($canonical) {
                return $canonical;
            }
        }

        return $url;
    }

    private function lastModified(Response $response): ?string
    {
        $header = $response->header('Last-Modified');
        if (!$header) {
            return null;
        }

        $time = strtotime($header);
        return $time ? gmdate('c', $time) : null;
    }

    /**
     * Ubah href menjadi URL absolut yang bersih, atau null kalau bukan halaman internal.
     */
    private function resolve(string $href, string $current): ?string
    {
        $href = trim($href);
        if ($href === '' || preg_match('/^(#|mailto:|tel:|javascript:|data:|whatsapp:|sms:)/i', $href)) {
            return null;
        }

        if (str_starts_with($href, '//')) {
            $href = $this->scheme . ':' . $href;
        } elseif (!preg_match('#^https?://#i', $href)) {
            $base = parse_url($current);
            $origin = $base['scheme'] . '://' . $base['host'];

            if (str_starts_with($href, '/')) {
                $href = $origin . $href;
            } else {
                $dir = rtrim(substr($base['path'] ?? '/', 0, strrpos($base['path'] ?? '/', '/') + 1), '/');
                $href = $origin . $dir . '/' . $href;
            }
        }

        $parts = parse_url($href);
        if (empty($parts['host']) || strtolower($parts['host']) !== $this->host) {
            return null;
        }

        $ext = strtolower(pathinfo($parts['path'] ?? '', PATHINFO_EXTENSION));
        if ($ext && in_array($ext, self::SKIP_EXTENSIONS, true)) {
            return null;
        }

        return $this->normalize($href, $href);
    }

    /**
     * Buang fragment dan query, rapikan path, hapus trailing slash (kecuali root).
     */
    private function normalize(string $url, string $fallback): string
    {
        $parts = parse_url($url) ?: parse_url($fallback);

        $segments = [];
        foreach (explode('/', $parts['path'] ?? '/') as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }
            if ($segment === '..') {
                array_pop($segments);
                continue;
            }
            $segments[] = $segment;
        }

        return $this->scheme . '://' . $this->host . '/' . implode('/', $segments);
    }

    private function buildXml(array $found): string
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        ksort($found);
        foreach ($found as $url => $lastmod) {
            $xml->startElement('url');
            $xml->writeElement('loc', $url);
            if ($lastmod) {
                $xml->writeElement('lastmod', $lastmod);
            }
            $xml->endElement();
        }

        $xml->endElement();
        $xml->endDocument();

        return $xml->outputMemory();
    }
}
