<?php

namespace Fidum\NovaPackageBundler\Contracts\Collections;

use Fidum\NovaPackageBundler\Contracts\Filters\Filter;
use Illuminate\Support\Enumerable;

interface AssetCollection extends Enumerable
{
    public function applyFilter(Filter $filter): AssetCollection;

    public function rejectFilter(Filter $filter): AssetCollection;
}
