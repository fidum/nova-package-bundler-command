<?php

namespace Fidum\NovaPackageBundler\Http\Middleware;

use Closure;
use Fidum\NovaPackageBundler\Contracts\Services\ScriptAssetService;
use Fidum\NovaPackageBundler\Contracts\Services\StyleAssetService;
use Illuminate\Http\Request;
use Laravel\Nova\Nova;

class OverrideNovaPackagesMiddleware
{
    public function __construct(
        protected ScriptAssetService $scriptAssetService,
        protected StyleAssetService $styleAssetService,
    ) {}

    public function handle(Request $request, Closure $next)
    {
        Nova::$scripts = $this->scriptAssetService->excluded()->toArray();
        Nova::$styles = $this->styleAssetService->excluded()->toArray();

        Nova::remoteScript(asset($this->scriptAssetService->outputPath()));
        Nova::remoteStyle(asset($this->styleAssetService->outputPath()));

        return $next($request);
    }
}
