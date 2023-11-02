<?php

namespace Fidum\NovaPackageBundler\Contracts\Filters;

use Laravel\Nova\Asset;

interface Filter
{
    public function apply(Asset $asset): bool;
}
