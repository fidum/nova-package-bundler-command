<?php

namespace Fidum\NovaPackageBundler\Collections;

use Fidum\NovaPackageBundler\Contracts\Collections\FilterCollection as FilterCollectionContract;
use Fidum\NovaPackageBundler\Contracts\Filters\Filter;
use Illuminate\Support\Collection;
use Laravel\Nova\Asset;

class FilterCollection extends Collection implements FilterCollectionContract
{
    public function apply(Asset $asset): bool
    {
        return $this->every(function (string $filterClass) use ($asset) {
            /** @var Filter $filter */
            $filter = app($filterClass);

            return $filter->apply($asset);
        });
    }
}
