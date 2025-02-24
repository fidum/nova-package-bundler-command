<?php

namespace Fidum\NovaPackageBundler\Services\Concerns;

trait BuildsOutputPath
{
    public function getLocalOutputPath(): string
    {
        return $this->outputPath;
    }

    public function getVersionedOutputPath(): string
    {
        $path = $this->getLocalOutputPath();

        return $this->manifestReaderService->getPath($path);
    }
}
