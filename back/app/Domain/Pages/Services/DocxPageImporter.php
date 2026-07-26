<?php

namespace App\Domain\Pages\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Lightweight .docx → ordered content blocks (no PhpWord dependency).
 */
class DocxPageImporter
{
    /**
     * @return array{title: string, seo_description: ?string, blocks: array<int, array<string, mixed>>}
     */
    public function import(UploadedFile $file): array
    {
        $zip = new ZipArchive();
        $path = $file->getRealPath();
        if ($path === false || $zip->open($path) !== true) {
            throw new \RuntimeException('Could not open Word document.');
        }

        $documentXml = $zip->getFromName('word/document.xml');
        $relsXml = $zip->getFromName('word/_rels/document.xml.rels') ?: '';
        if ($documentXml === false) {
            $zip->close();
            throw new \RuntimeException('Invalid Word document (missing document.xml).');
        }

        $relMap = $this->parseRelationships($relsXml);
        $blocks = [];
        $title = '';

        $dom = new \DOMDocument();
        $dom->loadXML($documentXml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

        foreach ($xpath->query('//w:body/*') as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }

            if ($node->localName === 'p') {
                $style = $this->paragraphStyle($xpath, $node);
                $html = $this->paragraphHtml($xpath, $node);
                $plain = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                // Images embedded in the paragraph
                foreach ($this->extractImagesFromParagraph($xpath, $node, $relMap, $zip) as $imageBlock) {
                    $blocks[] = $imageBlock;
                }

                if ($plain === '') {
                    continue;
                }

                if (preg_match('/^Heading([1-3])$/i', $style, $m) || preg_match('/^Title$/i', $style)) {
                    $level = isset($m[1]) ? (int) $m[1] : 1;
                    if ($title === '' && $level === 1) {
                        $title = $plain;
                    }
                    $blocks[] = [
                        'id' => (string) Str::uuid(),
                        'type' => 'heading',
                        'data' => ['level' => $level, 'text' => $plain],
                    ];
                    continue;
                }

                $blocks[] = [
                    'id' => (string) Str::uuid(),
                    'type' => 'richtext',
                    'data' => ['html' => $html !== '' ? $html : '<p>' . e($plain) . '</p>'],
                ];
            }
        }

        $zip->close();

        if ($title === '') {
            $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $title = trim(preg_replace('/[_-]+/', ' ', (string) $title) ?? 'Untitled page');
        }

        $seoDescription = null;
        $blocks = $this->postProcessBlocks($blocks, $seoDescription);

        return [
            'title' => $title,
            'seo_description' => $seoDescription,
            'blocks' => $blocks,
        ];
    }

    /**
     * Pull SEO meta + collapse FAQ Q/A headings into a faq block; drop bare image-credit URLs.
     *
     * @param  array<int, array<string, mixed>>  $blocks
     * @return array<int, array<string, mixed>>
     */
    private function postProcessBlocks(array $blocks, ?string &$seoDescription): array
    {
        $out = [];
        $count = count($blocks);
        for ($i = 0; $i < $count; $i++) {
            $block = $blocks[$i];
            $type = $block['type'] ?? '';
            $text = '';
            if ($type === 'heading') {
                $text = trim((string) ($block['data']['text'] ?? ''));
            } elseif ($type === 'richtext') {
                $text = trim(html_entity_decode(strip_tags((string) ($block['data']['html'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            }

            // Meta Description: heading + next paragraph → SEO field
            if ($type === 'heading' && preg_match('/^meta\s*description:?$/i', $text)) {
                $next = $blocks[$i + 1] ?? null;
                if ($next && ($next['type'] ?? '') === 'richtext') {
                    $seoDescription = trim(html_entity_decode(strip_tags((string) ($next['data']['html'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    $i++;
                }
                continue;
            }

            // Bare URL lines (often image credits from Word)
            if ($type === 'richtext' && preg_match('#^https?://\S+$#i', $text)) {
                continue;
            }

            // FAQs section → single faq block
            if ($type === 'heading' && preg_match('/^faqs?$/i', $text)) {
                $items = [];
                while ($i + 1 < $count) {
                    $q = $blocks[$i + 1] ?? null;
                    $a = $blocks[$i + 2] ?? null;
                    if (! $q || ($q['type'] ?? '') !== 'heading') {
                        break;
                    }
                    $qText = trim((string) ($q['data']['text'] ?? ''));
                    if ($qText === '' || preg_match('/^faqs?$/i', $qText)) {
                        break;
                    }
                    $answer = '';
                    if ($a && ($a['type'] ?? '') === 'richtext') {
                        $answer = trim(html_entity_decode(strip_tags((string) ($a['data']['html'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                        $i += 2;
                    } else {
                        $i += 1;
                    }
                    $items[] = ['question' => $qText, 'answer' => $answer];
                }
                if ($items !== []) {
                    $out[] = [
                        'id' => (string) Str::uuid(),
                        'type' => 'faq',
                        'data' => ['items' => $items],
                    ];
                }
                continue;
            }

            $out[] = $block;
        }

        return $out;
    }

    private function parseRelationships(string $relsXml): array
    {
        $map = [];
        if ($relsXml === '') {
            return $map;
        }
        $dom = new \DOMDocument();
        $dom->loadXML($relsXml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        foreach ($dom->getElementsByTagName('Relationship') as $rel) {
            if (! $rel instanceof \DOMElement) {
                continue;
            }
            $id = $rel->getAttribute('Id');
            $target = $rel->getAttribute('Target');
            if ($id && $target) {
                $map[$id] = ltrim(str_replace('\\', '/', $target), '/');
                if (! str_starts_with($map[$id], 'word/')) {
                    $map[$id] = 'word/' . $map[$id];
                }
            }
        }
        return $map;
    }

    private function paragraphStyle(\DOMXPath $xpath, \DOMElement $p): string
    {
        $nodes = $xpath->query('.//w:pStyle/@w:val', $p);
        if ($nodes && $nodes->length > 0) {
            return (string) $nodes->item(0)->nodeValue;
        }
        return '';
    }

    private function paragraphHtml(\DOMXPath $xpath, \DOMElement $p): string
    {
        $parts = [];
        foreach ($xpath->query('.//w:r', $p) as $run) {
            if (! $run instanceof \DOMElement) {
                continue;
            }
            $textNodes = $xpath->query('.//w:t', $run);
            $text = '';
            foreach ($textNodes as $t) {
                $text .= $t->textContent;
            }
            if ($text === '') {
                continue;
            }
            $bold = $xpath->query('.//w:b[not(@w:val="0") and not(@w:val="false")]', $run)->length > 0;
            $italic = $xpath->query('.//w:i[not(@w:val="0") and not(@w:val="false")]', $run)->length > 0;
            $chunk = e($text);
            if ($bold) {
                $chunk = '<strong>' . $chunk . '</strong>';
            }
            if ($italic) {
                $chunk = '<em>' . $chunk . '</em>';
            }
            $parts[] = $chunk;
        }
        if ($parts === []) {
            return '';
        }
        return '<p>' . implode('', $parts) . '</p>';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function extractImagesFromParagraph(\DOMXPath $xpath, \DOMElement $p, array $relMap, ZipArchive $zip): array
    {
        $out = [];
        foreach ($xpath->query('.//a:blip', $p) as $blip) {
            if (! $blip instanceof \DOMElement) {
                continue;
            }
            $embed = $blip->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'embed')
                ?: $blip->getAttribute('r:embed');
            if (! $embed || ! isset($relMap[$embed])) {
                continue;
            }
            $mediaPath = $relMap[$embed];
            $binary = $zip->getFromName($mediaPath);
            if ($binary === false) {
                continue;
            }
            $ext = strtolower(pathinfo($mediaPath, PATHINFO_EXTENSION) ?: 'png');
            if (! in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'], true)) {
                $ext = 'png';
            }
            $name = 'pages/' . time() . '_' . Str::random(8) . '.' . $ext;
            Storage::disk('public')->put($name, $binary);
            $out[] = [
                'id' => (string) Str::uuid(),
                'type' => 'image',
                'data' => [
                    'url' => '/storage/' . $name,
                    'alt' => '',
                ],
            ];
        }
        return $out;
    }
}
