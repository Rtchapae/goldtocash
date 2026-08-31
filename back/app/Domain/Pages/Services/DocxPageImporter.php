<?php

namespace App\Domain\Pages\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * Lightweight .docx → ordered content blocks (no PhpWord dependency).
 * Preserves bold/italic/underline, hyperlinks, and basic run fonts.
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
                $html = $this->paragraphHtml($xpath, $node, $relMap);
                $plain = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

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
                    'data' => [
                        'html' => $html !== '' ? $html : '<p>' . e($plain) . '</p>',
                        'fontFamily' => '',
                    ],
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

            if ($type === 'heading' && preg_match('/^meta\s*description:?$/i', $text)) {
                $next = $blocks[$i + 1] ?? null;
                if ($next && ($next['type'] ?? '') === 'richtext') {
                    $seoDescription = trim(html_entity_decode(strip_tags((string) ($next['data']['html'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    $i++;
                }
                continue;
            }

            if ($type === 'richtext' && preg_match('#^https?://\S+$#i', $text)) {
                continue;
            }

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
                        // Keep HTML (links/bold) from Word answer paragraphs
                        $answer = (string) ($a['data']['html'] ?? '');
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

    /**
     * @return array<string, array{target: string, external: bool}>
     */
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
            $type = $rel->getAttribute('Type');
            $mode = $rel->getAttribute('TargetMode');
            if (! $id || ! $target) {
                continue;
            }
            $external = strcasecmp($mode, 'External') === 0 || str_contains($type, '/hyperlink');
            $path = ltrim(str_replace('\\', '/', $target), '/');
            if (! $external && ! str_starts_with($path, 'word/')) {
                $path = 'word/' . $path;
            }
            $map[$id] = [
                'target' => $external ? $target : $path,
                'external' => $external,
            ];
        }
        return $map;
    }

    private function paragraphStyle(\DOMXPath $xpath, \DOMElement $p): string
    {
        $nodes = $xpath->query('./w:pPr/w:pStyle/@w:val', $p);
        if ($nodes && $nodes->length > 0) {
            return (string) $nodes->item(0)->nodeValue;
        }
        return '';
    }

    /**
     * @param  array<string, array{target: string, external: bool}>  $relMap
     */
    private function paragraphHtml(\DOMXPath $xpath, \DOMElement $p, array $relMap): string
    {
        $parts = [];
        foreach ($p->childNodes as $child) {
            if (! $child instanceof \DOMElement) {
                continue;
            }
            if ($child->localName === 'hyperlink') {
                $rid = $child->getAttributeNS('http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'id')
                    ?: $child->getAttribute('r:id');
                $inner = $this->runsHtml($xpath, $child);
                if ($inner === '') {
                    continue;
                }
                $url = '';
                if ($rid && isset($relMap[$rid]) && $relMap[$rid]['external']) {
                    $url = $relMap[$rid]['target'];
                }
                if ($url !== '' && preg_match('#^https?://#i', $url)) {
                    $parts[] = '<a href="' . e($url) . '" target="_blank" rel="noopener noreferrer">' . $inner . '</a>';
                } else {
                    $parts[] = $inner;
                }
                continue;
            }
            if ($child->localName === 'r') {
                $chunk = $this->runHtml($xpath, $child);
                if ($chunk !== '') {
                    $parts[] = $chunk;
                }
            }
        }
        if ($parts === []) {
            return '';
        }
        return '<p>' . implode('', $parts) . '</p>';
    }

    private function runsHtml(\DOMXPath $xpath, \DOMElement $parent): string
    {
        $parts = [];
        foreach ($xpath->query('./w:r', $parent) as $run) {
            if (! $run instanceof \DOMElement) {
                continue;
            }
            $chunk = $this->runHtml($xpath, $run);
            if ($chunk !== '') {
                $parts[] = $chunk;
            }
        }
        return implode('', $parts);
    }

    private function runHtml(\DOMXPath $xpath, \DOMElement $run): string
    {
        $textNodes = $xpath->query('.//w:t', $run);
        $text = '';
        foreach ($textNodes as $t) {
            $text .= $t->textContent;
        }
        if ($text === '') {
            return '';
        }

        $bold = $xpath->query('./w:rPr/w:b[not(@w:val="0") and not(@w:val="false")]', $run)->length > 0;
        $italic = $xpath->query('./w:rPr/w:i[not(@w:val="0") and not(@w:val="false")]', $run)->length > 0;
        $underline = $xpath->query('./w:rPr/w:u[not(@w:val="none")]', $run)->length > 0;

        $font = '';
        $fontNodes = $xpath->query('./w:rPr/w:rFonts/@w:ascii|./w:rPr/w:rFonts/@w:hAnsi', $run);
        if ($fontNodes && $fontNodes->length > 0) {
            $font = trim((string) $fontNodes->item(0)->nodeValue);
        }

        $sizePx = null;
        $szNodes = $xpath->query('./w:rPr/w:sz/@w:val', $run);
        if ($szNodes && $szNodes->length > 0) {
            $halfPoints = (int) $szNodes->item(0)->nodeValue;
            if ($halfPoints > 0) {
                $sizePx = max(10, (int) round($halfPoints / 2));
            }
        }

        $chunk = e($text);
        $styles = [];
        if ($font !== '') {
            $styles[] = 'font-family:' . e($font);
        }
        if ($sizePx !== null) {
            $styles[] = 'font-size:' . $sizePx . 'px';
        }
        if ($styles !== []) {
            $chunk = '<span style="' . implode(';', $styles) . '">' . $chunk . '</span>';
        }
        if ($bold) {
            $chunk = '<strong>' . $chunk . '</strong>';
        }
        if ($italic) {
            $chunk = '<em>' . $chunk . '</em>';
        }
        if ($underline) {
            $chunk = '<u>' . $chunk . '</u>';
        }

        return $chunk;
    }

    /**
     * @param  array<string, array{target: string, external: bool}>  $relMap
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
            $mediaPath = $relMap[$embed]['target'];
            if ($relMap[$embed]['external']) {
                continue;
            }
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
                    'align' => 'center',
                    'widthPercent' => 100,
                    'maxWidth' => null,
                ],
            ];
        }
        return $out;
    }
}
