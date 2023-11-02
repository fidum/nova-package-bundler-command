<?php

namespace Fidum\NovaPackageBundler\Filters;

use Fidum\NovaPackageBundler\Contracts\Filters\ScriptExcludedFilter as ScriptExcludedFilterContract;
use Fidum\NovaPackageBundler\Filters\Concerns\FiltersExcludedAssets;

class ScriptExcludedFilter implements ScriptExcludedFilterContract
{
    use FiltersExcludedAssets;

    public function __construct(protected array $exclusions) {}
}
