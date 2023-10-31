<?php

use Fidum\NovaPackageBundler\Http\Middleware\OverrideNovaPackagesMiddleware;
use Fidum\NovaPackageBundler\Tests\Support\TestTool;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Nova;

use function Pest\testDirectory;

it('replaces registered styles and scripts with the bundled files', function () {
    $this->app->setBasePath(testDirectory('fixtures'));
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'public');

    Nova::script('test-package', testDirectory('fixtures/input/test.js'));
    Nova::style('test-package', testDirectory('fixtures/input/test.css'));
    Nova::remoteScript('/input/public.js');
    Nova::remoteStyle('/input/public.css');

    expect(Nova::$scripts)->toHaveCount(2)->and(Nova::$styles)->toHaveCount(2);

    $middleware = new OverrideNovaPackagesMiddleware();
    $middleware->handle(request(), fn () => null);

    expect(Nova::$scripts)->toHaveCount(1)->and(Nova::$styles)->toHaveCount(1)
        ->and(Nova::$scripts[0])->path()->toEqual('http://localhost/vendor/nova-tools/app.js')
        ->and(Nova::$styles[0])->path()->toEqual('http://localhost/vendor/nova-tools/app.css');
});

it('replaces registered styles and scripts with the bundled files and skips assets from tool-boot-package', function () {
    $this->app->setBasePath(testDirectory('fixtures'));
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'public');

    Config::set('nova-package-bundler-command.excluded.scripts', ['tool-boot-package']);
    Config::set('nova-package-bundler-command.excluded.styles', ['tool-boot-package']);

    Nova::$tools = [TestTool::make()];
    Nova::script('tool-boot-package', testDirectory('fixtures/input/tool/tool.js'));
    Nova::style('tool-boot-package', testDirectory('fixtures/input/tool/tool.css'));
    Nova::script('test-package', testDirectory('fixtures/input/test.js'));
    Nova::style('test-package', testDirectory('fixtures/input/test.css'));
    Nova::remoteScript('/input/public.js');
    Nova::remoteStyle('/input/public.css');

    expect(Nova::$scripts)->toHaveCount(3)->and(Nova::$styles)->toHaveCount(3);

    $middleware = new OverrideNovaPackagesMiddleware();
    $middleware->handle(request(), fn () => null);

    expect(Nova::$scripts)->toHaveCount(2)->and(Nova::$styles)->toHaveCount(2)
        ->and(Nova::$scripts[0])->path()->toEqual('tests/fixtures/input/tool/tool.js')
        ->and(Nova::$scripts[1])->path()->toEqual('http://localhost/vendor/nova-tools/app.js')
        ->and(Nova::$styles[0])->path()->toEqual('tests/fixtures/input/tool/tool.css')
        ->and(Nova::$styles[1])->path()->toEqual('http://localhost/vendor/nova-tools/app.css');
});

afterEach(function () {
    Nova::$scripts = [];
    Nova::$styles = [];
});
