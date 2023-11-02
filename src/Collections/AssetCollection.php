<?php

namespace Fidum\NovaPackageBundler\Collections;

use Fidum\NovaPackageBundler\Contracts\Collections\AssetCollection as AssetCollectionContract;
use Fidum\NovaPackageBundler\Contracts\Collections\FilterCollection as FilterCollectionContract;
use Illuminate\Support\Collection;

class AssetCollection extends Collection implements AssetCollectionContract
{
    public function applyFilters(FilterCollectionContract $filters): AssetCollectionContract
    {
        return $this->filter($filters->apply(...));
    }

    public function rejectFilters(FilterCollectionContract $filters): AssetCollectionContract
    {
        return $this->reject($filters->apply(...));
    }
}
