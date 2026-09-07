<?php

namespace JeffersonGoncalves\MixpanelApi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MixpanelApiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('mixpanel-api')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(MixpanelApi::class, function () {
            return new MixpanelApi(
                config('mixpanel-api.token'),
                config('mixpanel-api.api_key'),
                config('mixpanel-api.secret'),
                config('mixpanel-api.project_id'),
            );
        });
    }
}
