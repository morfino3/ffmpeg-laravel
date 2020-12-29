<?php

namespace Laboratory\FFMpegLaravel\Facades;

use Illuminate\Support\Facades\Facade as BaseFacade;

class FFMpegLaravel extends BaseFacade
{
	/**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ffmpeglaravel';
    }
}