<?php

namespace Fidum\NovaPackageBundler\Contracts\Services;

interface ManifestBuilderService
{
    public function push(string $path, string $content): void;

    public function enabled(): bool;

    public function json(): string;

    public function manifestPath(): string;
}
