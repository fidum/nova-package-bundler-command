<?php

namespace Fidum\NovaPackageBundler\Tests\Support;

use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

use function Pest\testDirectory;

class TestTool extends Tool
{
    public function boot()
    {
        Nova::script('tool-boot-package', testDirectory('fixtures/input/tool/tool.js'));
        Nova::style('tool-boot-package', testDirectory('fixtures/input/tool/tool.css'));
    }

    public function menu(Request $request)
    {
        // Should never be called
    }
}
