<?php

namespace Fidum\NovaPackageBundler\Services\Concerns;

use Fidum\NovaPackageBundler\Contracts\Collections\AssetCollection as AssetCollectionContract;

trait CollectsAllowedAssets
{
    public function allowed(): AssetCollectionContract
    {
        return $this->collect()->applyFilter($this->filter)->values();
    }
}
