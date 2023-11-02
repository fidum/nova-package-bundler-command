<?php

namespace Fidum\NovaPackageBundler\Filters;

use Fidum\NovaPackageBundler\Contracts\Filters\StyleExcludedFilter as StyleExcludedFilterContract;
use Fidum\NovaPackageBundler\Filters\Concerns\FiltersExcludedAssets;

class StyleExcludedFilter implements StyleExcludedFilterContract
{
    use FiltersExcludedAssets;

    public function __construct(protected array $exclusions) {}
}
