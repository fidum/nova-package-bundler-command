<?php

namespace Fidum\NovaPackageBundler\Contracts\Collections;

use Illuminate\Support\Enumerable;
use Laravel\Nova\Asset;

interface FilterCollection extends Enumerable
{
    public function apply(Asset $asset): bool;
}
