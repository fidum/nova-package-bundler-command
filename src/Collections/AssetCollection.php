<?php

namespace Fidum\NovaPackageBundler\Collections;

use Fidum\NovaPackageBundler\Contracts\Collections\AssetCollection as AssetCollectionContract;
use Illuminate\Support\Collection;

class AssetCollection extends Collection implements AssetCollectionContract
{
    public function applyFilters(\Fidum\NovaPackageBundler\Contracts\Collections\FilterCollection $filters): AssetCollectionContract
    {
        return $this->filter($filters->apply(...));
    }

    public function rejectFilters(\Fidum\NovaPackageBundler\Contracts\Collections\FilterCollection $filters): AssetCollectionContract
    {
        return $this->reject($filters->apply(...));
    }
}
