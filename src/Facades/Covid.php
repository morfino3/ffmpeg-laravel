<?php

namespace Laboratory\Covid\Facades;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Covid extends BaseFacade
{
	/**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'covid';
    }
}