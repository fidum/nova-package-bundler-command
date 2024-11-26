<?php

namespace Fidum\NovaPackageBundler\Contracts\Services;

interface ManifestReaderService
{
    public function getPath(string $path): string;
}
