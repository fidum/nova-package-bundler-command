<?php

namespace Fidum\NovaPackageBundler;

use Fidum\NovaPackageBundler\Collections\FilterCollection;
use Fidum\NovaPackageBundler\Commands\PublishCommand;
use Fidum\NovaPackageBundler\Contracts\Collections\FilterCollection as FilterCollectionContract;
use Fidum\NovaPackageBundler\Contracts\Filters\ScriptExcludedFilter as ScriptExcludedFilterContract;
use Fidum\NovaPackageBundler\Contracts\Filters\StyleExcludedFilter as StyleExcludedFilterContract;
use Fidum\NovaPackageBundler\Contracts\Filters\UrlAssetsExcludedFilter as UrlAssetsExcludedFilterContract;
use Fidum\NovaPackageBundler\Contracts\Services\ScriptAssetService as ScriptAssetServiceContract;
use Fidum\NovaPackageBundler\Contracts\Services\StyleAssetService as StyleAssetServiceContract;
use Fidum\NovaPackageBundler\Filters\ScriptExcludedFilter;
use Fidum\NovaPackageBundler\Filters\StyleExcludedFilter;
use Fidum\NovaPackageBundler\Filters\UrlAssetsExcludedFilter;
use Fidum\NovaPackageBundler\Services\ScriptAssetService;
use Fidum\NovaPackageBundler\Services\StyleAssetService;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Arr;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NovaPackageBundlerServiceProvider extends PackageServiceProvider implements DeferrableProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('nova-package-bundler-command')
            ->hasConfigFile()
            ->hasCommand(PublishCommand::class);
    }

    public function registeringPackage()
    {
        $this->app->bind(FilterCollectionContract::class, function (Container $app) {
            return FilterCollection::make([
                UrlAssetsExcludedFilterContract::class,
            ]);
        });

        $this->app->bind(ScriptExcludedFilterContract::class, function (Container $app) {
            return new ScriptExcludedFilter(
                Arr::wrap($app->make('config')->get('nova-package-bundler-command.excluded.scripts')),
            );
        });

        $this->app->bind(ScriptAssetServiceContract::class, function (Container $app) {
            return new ScriptAssetService(
                $app->make('config')->get('nova-package-bundler-command.paths.script'),
                $app->make(FilterCollectionContract::class)
                    ->push(ScriptExcludedFilterContract::class),
            );
        });

        $this->app->bind(StyleExcludedFilterContract::class, function (Container $app) {
            return new StyleExcludedFilter(
                Arr::wrap($app->make('config')->get('nova-package-bundler-command.excluded.styles')),
            );
        });

        $this->app->bind(UrlAssetsExcludedFilterContract::class, function (Container $app) {
            return new UrlAssetsExcludedFilter(
                (bool) $app->make('config')->get('nova-package-bundler-command.download_url_assets'),
            );
        });

        $this->app->bind(StyleAssetServiceContract::class, function (Container $app) {
            return new StyleAssetService(
                $app->make('config')->get('nova-package-bundler-command.paths.style'),
                $app->make(FilterCollectionContract::class)
                    ->push(StyleExcludedFilterContract::class),
            );
        });
    }

    public function provides()
    {
        return [
            FilterCollectionContract::class,
            ScriptAssetServiceContract::class,
            ScriptExcludedFilterContract::class,
            StyleAssetServiceContract::class,
            StyleExcludedFilterContract::class,
            UrlAssetsExcludedFilterContract::class,
        ];
    }
}
