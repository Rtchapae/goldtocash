<?php

namespace App\Console\Commands;

use App\Domain\Posts\Models\Post;
use Illuminate\Console\Command;

class AddInternalLinksCommand extends Command
{
    protected $signature = 'posts:add-internal-links {--dry-run : Preview changes without saving}';

    protected $description = 'Add internal links to /sell blog posts (one-time SEO hyperlink project)';

    /**
     * slug => list of [anchor_text, target_path]
     *
     * @var array<string, list<array{0: string, 1: string}>>
     */
    private const LINKS = [
        'selling-inherited-gold-jewelry' => [
            ['Damaged gold jewelry', '/what-we-buy'],
            ['Our pricing sets us apart', '/what-we-pay'],
            ['Damaged gold jewelry', '/sell/sell-damaged-gold-jewelery'],
        ],
        'selling-gold-tennis-bracelets' => [
            ['vintage styles', '/sell/sell-vintage-gold-jewelry'],
            ['Deep symbolism', '/sell/sell-new-gold-jewelry'],
        ],
        'sell-your-gold-jewelry' => [
            ['Best value for your gold jewelry', '/gold-calculator'],
            ['Gold necklace', '/sell/sell-gold-necklace'],
            ['Immediate cash', '/sell/sell-gold-for-cash'],
        ],
        'sell-vintage-gold-jewelry' => [
            ['Pendants', '/sell/sell-gold-jewelry-pendants'],
            ['understanding the appraisal process is crucial', '/sell/jewelry-appraisal'],
        ],
        'sell-used-gold-jewelry' => [
            ['simple gold bracelet', '/sell/selling-gold-tennis-bracelets'],
            ['engagement rings', '/what-we-buy'],
        ],
        'sell-rose-gold-jewelry' => [
            ['Watches and bracelets', '/sell-luxury-watches'],
            ['resale value', '/sell/jewelry-appraisal'],
        ],
        'sell-new-gold-jewelry' => [
            ['Gold jewelry', '/sell/sell-gold-jewelry'],
            ['monetary value', '/gold-calculator'],
        ],
        'sell-gold-pendant' => [
            ['cameos became a favored means of expression', '/sell/sell-gold-pendant'],
            ['top-notch value', '/gold-calculator'],
        ],
        'sell-gold-online' => [
            ['value of your jewelry collection', '/gold-calculator'],
            ["Don't settle for a single appraisal", '/sell/jewelry-appraisal'],
            ['reputation of gold buyers', '/sell/online-gold-buyer'],
        ],
        'sell-gold-necklace' => [
            ["the necklace's value", '/gold-calculator'],
            ['charm of vintage pieces', '/sell/sell-vintage-gold-jewelry'],
            ['various pendants', '/sell/sell-gold-jewelry-pendants'],
        ],
        'sell-gold-jewelry-pendants' => [
            ['symbolic', '/sell/sell-new-gold-jewelry'],
            ['perceived value', '/gold-calculator'],
        ],
        'sell-gold-jewelry' => [
            ['receiving a fair market price', '/sell/jewelry-appraisal'],
            ['where to sell my gold jewelry', '/sell/sell-your-gold-jewelry'],
            ['fair and transparent offer', '/what-we-pay'],
        ],
        'sell-gold-for-cash' => [
            ['Sell your gold jewelry online now', '/sell/sell-gold-online'],
            ['hassle-free process', '/how-it-works'],
            ['Trusted online gold buyer', '/sell/online-gold-buyer'],
        ],
        'sell-gold-bars' => [
            ['jewelry industry', '/sell/sell-gold-jewelry'],
            ['Selling your gold', '/sell/sell-gold-online'],
        ],
        'sell-damaged-gold-jewelery' => [
            ['necklace', '/sell/sell-gold-necklace'],
            ['gold jewelry online', '/sell/sell-gold-online'],
            ['fair price', '/gold-calculator'],
        ],
        'sell-body-jewelry' => [
            ['all kinds of items', '/what-we-buy'],
            ['the selling process', '/how-it-works'],
        ],
        'online-gold-buyer' => [
            ['unwanted pieces into cash', '/sell/sell-damaged-gold-jewelery'],
            ['seamless process', '/how-it-works'],
            ['selling gold jewelry online', '/sell/sell-gold-online'],
        ],
        'jewelry-appraisal' => [
            ['accurate jewelry appraisals', '/gold-calculator'],
            ['gold necklace', '/sell/sell-gold-necklace'],
            ['online markets', '/sell/sell-gold-online'],
        ],
        'gold-price-vs-jewelry-price' => [
            ['price of gold', '/gold-calculator'],
            ['expert appraisal', '/sell/jewelry-appraisal'],
            ['gold bullion or coins', '/sell-gold-coins'],
        ],
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $linked = 0;
        $alreadyLinked = 0;
        $notFound = 0;
        $missingPost = 0;

        if ($dryRun) {
            $this->info('Dry run — no changes will be saved.');
        }

        foreach (self::LINKS as $slug => $links) {
            $post = Post::where('slug', $slug)
                ->where('path_prefix', '/sell')
                ->first();

            if (! $post) {
                $this->warn("Post not found: {$slug} (path_prefix=/sell)");
                $missingPost++;

                continue;
            }

            $body = $post->body ?? '';

            foreach ($links as [$anchorText, $targetPath]) {
                $result = $this->wrapFirstUnlinkedOccurrence($body, $anchorText, $targetPath);

                if ($result === null) {
                    if ($this->anchorAlreadyLinked($body, $anchorText, $targetPath)) {
                        $this->line("  [already linked] {$slug}: \"{$anchorText}\" -> {$targetPath}");
                        $alreadyLinked++;
                    } else {
                        $this->warn("  [not found] {$slug}: \"{$anchorText}\"");
                        $notFound++;
                    }

                    continue;
                }

                $body = $result;
                $this->line("  [linked] {$slug}: \"{$anchorText}\" -> {$targetPath}");
                $linked++;
            }

            if (! $dryRun && $body !== $post->body) {
                $post->body = $body;
                $post->save();
            }
        }

        $this->newLine();
        $this->info("Done: {$linked} linked, {$alreadyLinked} already linked, {$notFound} not found, {$missingPost} posts missing.");

        return self::SUCCESS;
    }

    private function anchorAlreadyLinked(string $html, string $anchorText, string $href): bool
    {
        $pattern = '/<a\s+[^>]*href=["\']'.preg_quote($href, '/').'["\'][^>]*>.*?'.preg_quote($anchorText, '/').'.*?<\/a>/is';

        if (preg_match($pattern, $html)) {
            return true;
        }

        $pos = stripos($html, $anchorText);

        return $pos !== false && $this->isInsideAnchorTag($html, $pos);
    }

    private function wrapFirstUnlinkedOccurrence(string $html, string $text, string $href): ?string
    {
        $offset = 0;
        $textLen = strlen($text);

        while (($pos = stripos($html, $text, $offset)) !== false) {
            if (! $this->isInsideAnchorTag($html, $pos)) {
                $actualText = substr($html, $pos, $textLen);
                $replacement = '<a href="'.htmlspecialchars($href, ENT_QUOTES, 'UTF-8').'">'.$actualText.'</a>';

                return substr($html, 0, $pos).$replacement.substr($html, $pos + $textLen);
            }

            $offset = $pos + $textLen;
        }

        return null;
    }

    private function isInsideAnchorTag(string $html, int $position): bool
    {
        if ($position < 0) {
            return false;
        }

        $before = substr($html, 0, $position);
        $lastOpenA = strripos($before, '<a ');
        $lastOpenA2 = strripos($before, '<a>');

        $lastOpen = -1;
        if ($lastOpenA !== false) {
            $lastOpen = $lastOpenA;
        }
        if ($lastOpenA2 !== false && $lastOpenA2 > $lastOpen) {
            $lastOpen = $lastOpenA2;
        }

        if ($lastOpen === -1) {
            return false;
        }

        $lastClose = strripos($before, '</a>');

        return $lastClose === false || $lastClose < $lastOpen;
    }
}
