<?php

namespace Fidum\NovaPackageBundler\Collections;

use Fidum\NovaPackageBundler\Contracts\Collections\AssetCollection as AssetCollectionContract;
use Fidum\NovaPackageBundler\Contracts\Filters\Filter;
use Illuminate\Support\Collection;

class AssetCollection extends Collection implements AssetCollectionContract
{
    public function applyFilter(Filter $filter): AssetCollectionContract
    {
        return $this->filter($filter->apply(...));
    }

    public function rejectFilter(Filter $filter): AssetCollectionContract
    {
        return $this->reject($filter->apply(...));
    }
}
