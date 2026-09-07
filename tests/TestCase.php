<?php

namespace Jeffersongoncalves\LaravelMixpanelApi\Tests;

use Jeffersongoncalves\LaravelMixpanelApi\LaravelMixpanelApiServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelMixpanelApiServiceProvider::class,
        ];
    }
}
