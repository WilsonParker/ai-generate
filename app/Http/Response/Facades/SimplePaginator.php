<?php

namespace App\Http\Response\Facades;

use Illuminate\Support\Facades\Facade;

class SimplePaginator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'simplePaginator'; }
}
