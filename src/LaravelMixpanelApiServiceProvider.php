<?php

namespace Jeffersongoncalves\LaravelMixpanelApi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelMixpanelApiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mixpanel-api')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
