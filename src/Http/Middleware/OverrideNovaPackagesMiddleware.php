<?php

namespace Fidum\NovaPackageBundler\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Nova\Nova;

class OverrideNovaPackagesMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        Nova::$scripts = collect(Nova::allScripts())->filter(function ($script) {
            return in_array($script->name(), config('nova-package-bundler-command.excluded.scripts', []));
        })->toArray();

        Nova::$styles = collect(Nova::allStyles())->filter(function ($style) {
            return in_array($style->name(), config('nova-package-bundler-command.excluded.styles', []));
        })->toArray();

        Nova::remoteScript(asset(config('nova-package-bundler-command.paths.script')));
        Nova::remoteStyle(asset(config('nova-package-bundler-command.paths.style')));

        return $next($request);
    }
}
