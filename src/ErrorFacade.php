<?php

namespace Error;

/**
 * This file is part of Prion Error,
 * setting data for Laravel & Lumen.
 *
 * @license MIT
 * @company Prion Development
 * @package Setting
 */

use Illuminate\Support\Facades\Facade;

class ErrorFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'error';
    }
}