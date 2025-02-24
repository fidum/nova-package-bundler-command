<?php

namespace Fidum\NovaPackageBundler\Contracts\Services;

use Fidum\NovaPackageBundler\Contracts\Collections\AssetCollection;

interface AssetService
{
    public function allowed(): AssetCollection;

    public function collect(): AssetCollection;

    public function excluded(): AssetCollection;

    public function getLocalOutputPath(): string;

    public function getVersionedOutputPath(): string;
}
