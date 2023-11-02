<?php

use Fidum\NovaPackageBundler\Http\Middleware\OverrideNovaPackagesMiddleware;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Nova;

use function Pest\testDirectory;

beforeEach(function () {
    $this->app->setBasePath(testDirectory('fixtures'));
    Nova::script('test-package', testDirectory('fixtures/input/test.js'));
    Nova::style('test-package', testDirectory('fixtures/input/test.css'));
    Nova::remoteScript('/input/public.js');
    Nova::remoteStyle('/input/public.css');
});

it('replaces registered styles and scripts with the bundled files', function () {
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'public');
    expect(Nova::$scripts)->toHaveCount(2)->and(Nova::$styles)->toHaveCount(2);

    $middleware = $this->app->make(OverrideNovaPackagesMiddleware::class);
    $middleware->handle(request(), fn () => null);

    expect(Nova::$scripts)->toHaveCount(1)
        ->and(Nova::$styles)->toHaveCount(1)
        ->and(Nova::$scripts[0])->path()->toEqual('http://localhost/vendor/nova-tools/app.js')
        ->and(Nova::$styles[0])->path()->toEqual('http://localhost/vendor/nova-tools/app.css');
});

it('replaces registered styles and scripts with the bundled files and excluded assets', function () {
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'public');
    expect(Nova::$scripts)->toHaveCount(2)->and(Nova::$styles)->toHaveCount(2);

    Config::set('nova-package-bundler-command.excluded.scripts', ['test-package']);
    Config::set('nova-package-bundler-command.excluded.styles', ['test-package']);

    $middleware = $this->app->make(OverrideNovaPackagesMiddleware::class);
    $middleware->handle(request(), fn () => null);

    expect(Nova::$scripts)->toHaveCount(2)
        ->and(Nova::$styles)->toHaveCount(2)
        ->and(Nova::$scripts[0])->path()->toEqual('tests/fixtures/input/test.js')
        ->and(Nova::$scripts[1])->path()->toEqual('http://localhost/vendor/nova-tools/app.js')
        ->and(Nova::$styles[0])->path()->toEqual('tests/fixtures/input/test.css')
        ->and(Nova::$styles[1])->path()->toEqual('http://localhost/vendor/nova-tools/app.css');
});

afterEach(function () {
    Nova::$scripts = [];
    Nova::$styles = [];
});
