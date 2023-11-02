<?php

namespace Fidum\NovaPackageBundler\Filters;

use Fidum\NovaPackageBundler\Concerns\IdentifiesUrls;
use Fidum\NovaPackageBundler\Contracts\Filters\SkipUrlAssetsFilter as SkipUrlAssetsFilterContract;
use Laravel\Nova\Asset;

class SkipUrlAssetsFilter implements SkipUrlAssetsFilterContract
{
    use IdentifiesUrls;

    public function __construct(protected bool $allowed) {}

    public function apply(Asset $asset): bool
    {
        if ($this->allowed) {
            return true;
        }

        if ($this->isUrl($asset->path())) {
            return false;
        }

        return true;
    }
}
