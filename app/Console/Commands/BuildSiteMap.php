<?php

namespace App\Console\Commands;

use App\Services\SiteMap\Facades\SiteMapService;
use Illuminate\Console\Command;

class BuildSiteMap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:build-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build site map and upload to s3';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SiteMapService::generate();
    }
}
