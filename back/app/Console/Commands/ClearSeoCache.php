<?php

namespace App\Console\Commands;

use App\Domain\Seo\Actions\GetSeoPageAction;
use Illuminate\Console\Command;

class ClearSeoCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:clear-cache {--all : Clear all SEO cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear SEO cache from Redis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $getAction = app(GetSeoPageAction::class);

        if ($this->option('all')) {
            $this->info('Clearing all SEO cache...');
            $getAction->clearAllCache();
            $this->info('All SEO cache cleared successfully!');
        } else {
            $this->info('Clearing SEO cache...');
            $getAction->clearAllCache();
            $this->info('SEO cache cleared successfully!');
        }

        return Command::SUCCESS;
    }
}
