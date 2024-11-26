<?php

namespace Fidum\NovaPackageBundler\Services;

use Fidum\NovaPackageBundler\Contracts\Services\ManifestReaderService as ManifestReaderServiceContract;
use Illuminate\Filesystem\Filesystem;

class ManifestReaderService implements ManifestReaderServiceContract
{
    public array $assets = [];

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly bool $enabled,
        private readonly string $manifestPath,
    ) {
        $path = public_path($this->manifestPath);

        if ($this->enabled && $this->filesystem->exists($path)) {
            $this->assets = $this->filesystem->json($path);
        }
    }

    public function getPath(string $path): string
    {
        return $this->assets[$path] ?? $path;
    }
}
