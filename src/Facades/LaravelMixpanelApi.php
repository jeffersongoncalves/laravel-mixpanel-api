<?php

namespace Jeffersongoncalves\LaravelMixpanelApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jeffersongoncalves\LaravelMixpanelApi\LaravelMixpanelApi
 */
class LaravelMixpanelApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-mixpanel-api';
    }
}
