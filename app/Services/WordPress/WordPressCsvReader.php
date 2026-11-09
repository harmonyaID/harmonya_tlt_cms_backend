<?php

namespace App\Services\WordPress;

class WordPressCsvReader
{
    public function read(string $csvPath): \Generator
    {
        $handle = fopen($csvPath, 'r');

        if (!$handle) {
            throw new \RuntimeException("Unable to open CSV file: {$csvPath}");
        }

        $header = fgetcsv($handle);

        if (!$header) {
            fclose($handle);
            return;
        }

        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        while (($row = fgetcsv($handle)) !== false) {
            if ($row === [null]) {
                continue;
            }

            yield array_combine($header, array_slice(array_pad($row, count($header), null), 0, count($header)));
        }

        fclose($handle);
    }

    public function readMedia(string $path): array
    {
        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'json') {
            $json = json_decode((string) file_get_contents($path), true);

            return is_array($json) ? array_map('strval', $json) : [];
        }

        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'xml') {
            return $this->readMediaFromWxr($path);
        }

        $map = [];
        foreach ($this->read($path) as $row) {
            $id = trim((string) ($row['ID'] ?? $row['id'] ?? ''));
            $url = '';
            foreach (['Attachment URL', 'URL', 'Image URL', 'guid', 'GUID', 'Permalink'] as $col) {
                $v = trim((string) ($row[$col] ?? ''));
                if (preg_match('#^https?://#i', $v)) {
                    $url = $v;
                    break;
                }
            }
            if ($id !== '' && $url !== '') {
                $map[$id] = $url;
            }
        }

        return $map;
    }

    private function readMediaFromWxr(string $path): array
    {
        $map = [];
        $xml = new \XMLReader();

        if (!$xml->open($path, null, LIBXML_NOCDATA | LIBXML_NONET)) {
            throw new \RuntimeException("Unable to open XML file: {$path}");
        }

        while ($xml->read()) {
            if ($xml->nodeType !== \XMLReader::ELEMENT || $xml->name !== 'item') {
                continue;
            }

            $item = simplexml_load_string($xml->readOuterXml(), 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET);
            if (!$item) {
                continue;
            }

            $ns = null;
            foreach ($item->getNamespaces(true) as $uri) {
                if (preg_match('#^http://wordpress\.org/export/[0-9.]+/$#', $uri)) {
                    $ns = $item->children($uri);
                    break;
                }
            }

            if ($ns && (string) $ns->post_type === 'attachment') {
                $id = trim((string) $ns->post_id);
                $url = trim((string) $ns->attachment_url);
                if ($id !== '' && $url !== '') {
                    $map[$id] = $url;
                }
            }
        }

        $xml->close();

        return $map;
    }
}
