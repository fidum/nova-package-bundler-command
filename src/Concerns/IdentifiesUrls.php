<?php

namespace Fidum\NovaPackageBundler\Concerns;

use Illuminate\Support\Str;

trait IdentifiesUrls
{
    private function isUrl(string $path): bool
    {
        return Str::startsWith($path, ['http://', 'https://', '://']);
    }
}
