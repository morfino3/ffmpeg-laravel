<?php

namespace Laboratory\Vidconvert\Facades;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Vidconvert extends BaseFacade
{
	/**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'vidconvert';
    }
}