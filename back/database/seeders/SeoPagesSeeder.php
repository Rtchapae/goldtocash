<?php

namespace Database\Seeders;

use App\Domain\Seo\Data\DefaultSeoPages;
use App\Domain\Seo\Models\SeoPage;
use Illuminate\Database\Seeder;

class SeoPagesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (DefaultSeoPages::all() as $pageData) {
            SeoPage::updateOrCreate(
                ['route_name' => $pageData['route_name']],
                $pageData
            );
        }
    }
}
