<?php

namespace Fidum\NovaPackageBundler;

use Fidum\NovaPackageBundler\Commands\PublishCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NovaPackageBundlerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('nova-package-bundler-command')
            ->hasConfigFile()
            ->hasCommand(PublishCommand::class);
    }
}
