<?php

namespace App\Services\SiteMap\Facades;

use Illuminate\Support\Facades\Facade;

class SiteMapService extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'siteMapService'; }
}
