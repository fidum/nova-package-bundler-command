<?php

namespace Fidum\NovaPackageBundler\Contracts\Services;

use Fidum\NovaPackageBundler\Contracts\Collections\AssetCollection;

interface AssetService
{
    public function collect(): AssetCollection;

    public function allowed(): AssetCollection;

    public function excluded(): AssetCollection;

    public function outputPath(): string;
}
