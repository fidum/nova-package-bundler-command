<?php

namespace Fidum\NovaPackageBundler\Services\Concerns;

use Fidum\NovaPackageBundler\Contracts\Collections\AssetCollection as AssetCollectionContract;

trait CollectsExcludedAssets
{
    public function excluded(): AssetCollectionContract
    {
        return $this->collect()->rejectFilters($this->filters)->values();
    }
}
