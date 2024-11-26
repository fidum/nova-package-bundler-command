<?php

namespace Fidum\NovaPackageBundler;

use Fidum\NovaPackageBundler\Collections\FilterCollection;
use Fidum\NovaPackageBundler\Commands\PublishCommand;
use Fidum\NovaPackageBundler\Contracts\Collections\FilterCollection as FilterCollectionContract;
use Fidum\NovaPackageBundler\Contracts\Filters\ScriptExcludedFilter as ScriptExcludedFilterContract;
use Fidum\NovaPackageBundler\Contracts\Filters\StyleExcludedFilter as StyleExcludedFilterContract;
use Fidum\NovaPackageBundler\Contracts\Filters\UrlAssetsExcludedFilter as UrlAssetsExcludedFilterContract;
use Fidum\NovaPackageBundler\Contracts\Services\ManifestBuilderService as ManifestWriterServiceContract;
use Fidum\NovaPackageBundler\Contracts\Services\ManifestReaderService as ManifestReaderServiceContract;
use Fidum\NovaPackageBundler\Contracts\Services\ScriptAssetService as ScriptAssetServiceContract;
use Fidum\NovaPackageBundler\Contracts\Services\StyleAssetService as StyleAssetServiceContract;
use Fidum\NovaPackageBundler\Filters\ScriptExcludedFilter;
use Fidum\NovaPackageBundler\Filters\StyleExcludedFilter;
use Fidum\NovaPackageBundler\Filters\UrlAssetsExcludedFilter;
use Fidum\NovaPackageBundler\Services\ManifestBuilderService;
use Fidum\NovaPackageBundler\Services\ManifestReaderService;
use Fidum\NovaPackageBundler\Services\ScriptAssetService;
use Fidum\NovaPackageBundler\Services\StyleAssetService;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Filesystem\Filesystem;
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

        $this->app->scoped(ManifestReaderServiceContract::class, function (Container $app) {
            return new ManifestReaderService(
                filesystem: $app->make(Filesystem::class),
                enabled: (bool) $app->make('config')->get('nova-package-bundler-command.version.enabled'),
                manifestPath: (string) $app->make('config')->get('nova-package-bundler-command.version.manifest'),
            );
        });

        $this->app->scoped(ManifestWriterServiceContract::class, function (Container $app) {
            return new ManifestBuilderService(
                enabled: (bool) $app->make('config')->get('nova-package-bundler-command.version.enabled'),
                manifestPath: (string) $app->make('config')->get('nova-package-bundler-command.version.manifest'),
            );
        });

        $this->app->bind(ScriptExcludedFilterContract::class, function (Container $app) {
            return new ScriptExcludedFilter(
                exclusions: Arr::wrap($app->make('config')->get('nova-package-bundler-command.excluded.scripts')),
            );
        });

        $this->app->bind(ScriptAssetServiceContract::class, function (Container $app) {
            return new ScriptAssetService(
                manifestReaderService: $app->make(ManifestReaderServiceContract::class),
                outputPath: $app->make('config')->get('nova-package-bundler-command.paths.script'),
                filters: $app->make(FilterCollectionContract::class)
                    ->push(ScriptExcludedFilterContract::class),
            );
        });

        $this->app->bind(StyleExcludedFilterContract::class, function (Container $app) {
            return new StyleExcludedFilter(
                exclusions: Arr::wrap($app->make('config')->get('nova-package-bundler-command.excluded.styles')),
            );
        });

        $this->app->bind(UrlAssetsExcludedFilterContract::class, function (Container $app) {
            return new UrlAssetsExcludedFilter(
                allowed: (bool) $app->make('config')->get('nova-package-bundler-command.download_url_assets'),
            );
        });

        $this->app->bind(StyleAssetServiceContract::class, function (Container $app) {
            return new StyleAssetService(
                manifestReaderService: $app->make(ManifestReaderServiceContract::class),
                outputPath: $app->make('config')->get('nova-package-bundler-command.paths.style'),
                filters: $app->make(FilterCollectionContract::class)
                    ->push(StyleExcludedFilterContract::class),
            );
        });
    }

    public function provides()
    {
        return [
            FilterCollectionContract::class,
            ManifestReaderServiceContract::class,
            ManifestBuilderService::class,
            ScriptAssetServiceContract::class,
            ScriptExcludedFilterContract::class,
            StyleAssetServiceContract::class,
            StyleExcludedFilterContract::class,
            UrlAssetsExcludedFilterContract::class,
        ];
    }
}
