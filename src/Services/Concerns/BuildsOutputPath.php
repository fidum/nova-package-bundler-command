<?php

namespace Fidum\NovaPackageBundler\Services\Concerns;

trait BuildsOutputPath
{
    public function outputPath(): string
    {
        return public_path($this->outputPath);
    }
}
