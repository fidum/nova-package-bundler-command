<?php

namespace Fidum\NovaPackageBundler\Contracts\Collections;

use Illuminate\Support\Enumerable;

interface AssetCollection extends Enumerable
{
    public function applyFilters(FilterCollection $filters): AssetCollection;

    public function rejectFilters(FilterCollection $filters): AssetCollection;
}
