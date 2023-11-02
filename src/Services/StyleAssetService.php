<?php

namespace Fidum\NovaPackageBundler\Services;

use Fidum\NovaPackageBundler\Collections\AssetCollection;
use Fidum\NovaPackageBundler\Contracts\Collections\AssetCollection as AssetCollectionContract;
use Fidum\NovaPackageBundler\Contracts\Filters\StyleExcludedFilter as StyleExcludedFilterContract;
use Fidum\NovaPackageBundler\Contracts\Services\StyleAssetService as StyleAssetServiceContract;
use Fidum\NovaPackageBundler\Services\Concerns\BuildsOutputPath;
use Fidum\NovaPackageBundler\Services\Concerns\CollectsAllowedAssets;
use Fidum\NovaPackageBundler\Services\Concerns\CollectsExcludedAssets;
use Laravel\Nova\Nova;

class StyleAssetService implements StyleAssetServiceContract
{
    use BuildsOutputPath;
    use CollectsAllowedAssets;
    use CollectsExcludedAssets;

    public function __construct(
        protected StyleExcludedFilterContract $filter,
        protected string $outputPath,
    ) {}

    public function collect(): AssetCollectionContract
    {
        return AssetCollection::make(Nova::allStyles());
    }
}
