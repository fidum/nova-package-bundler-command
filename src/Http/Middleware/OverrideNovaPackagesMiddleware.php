<?php

namespace Fidum\NovaPackageBundler\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Nova\Nova;

class OverrideNovaPackagesMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        Nova::$scripts = [];
        Nova::$styles = [];

        Nova::remoteScript(asset(config('nova-package-bundler-command.paths.script')));
        Nova::remoteStyle(asset(config('nova-package-bundler-command.paths.style')));

        return $next($request);
    }
}
