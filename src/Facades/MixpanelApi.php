<?php

namespace JeffersonGoncalves\MixpanelApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\MixpanelApi\MixpanelApi
 */
class MixpanelApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JeffersonGoncalves\MixpanelApi\MixpanelApi::class;
    }
}
