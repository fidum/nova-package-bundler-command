<?php

namespace Fidum\NovaPackageBundler\Services;

use Fidum\NovaPackageBundler\Contracts\Services\ManifestBuilderService as ManifestWriterServiceContract;

class ManifestBuilderService implements ManifestWriterServiceContract
{
    public array $assets = [];

    public function __construct(
        private readonly bool $enabled,
        private readonly string $manifestPath,
    ) {}

    public function push(string $path, string $content): void
    {
        $id = md5($content);

        $this->assets[$path] = $path.'?id='.$id;
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }

    public function json(): string
    {
        return json_encode($this->assets, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function manifestPath(): string
    {
        return $this->manifestPath;
    }
}
