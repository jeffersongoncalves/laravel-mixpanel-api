<?php

namespace JeffersonGoncalves\MixpanelApi\Tests;

use JeffersonGoncalves\MixpanelApi\MixpanelApiServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MixpanelApiServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('mixpanel-api.token', 'test-token');
        $app['config']->set('mixpanel-api.api_key', 'test-api-key');
        $app['config']->set('mixpanel-api.secret', 'test-secret');
        $app['config']->set('mixpanel-api.project_id', '12345');
    }
}
