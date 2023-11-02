<?php

namespace Fidum\NovaPackageBundler\Filters\Concerns;

use Laravel\Nova\Asset;

trait FiltersExcludedAssets
{
    public function apply(Asset $asset): bool
    {
        if (in_array($asset->name(), $this->exclusions, true)) {
            return false;
        }

        return true;
    }
}
