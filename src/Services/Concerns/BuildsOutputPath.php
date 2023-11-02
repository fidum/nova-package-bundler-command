<?php

namespace Fidum\NovaPackageBundler\Services\Concerns;

trait BuildsOutputPath
{
    public function outputPath(): string
    {
        return $this->outputPath;
    }
}
