<?php

namespace CapeAndBay\ZingFit\Facades;

use Illuminate\Support\Facades\Facade;

class ZingFit extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'zingfit';
    }
}
