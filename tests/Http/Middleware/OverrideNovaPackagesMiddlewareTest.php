<?php

use Fidum\NovaPackageBundler\Http\Middleware\OverrideNovaPackagesMiddleware;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Nova;

use function Pest\testDirectory;

beforeEach(function () {
    $this->app->setBasePath(testDirectory('fixtures'));
    Nova::remoteScript('https://example.com/index.js');
    Nova::remoteStyle('https://example.com/app.css');
    Nova::script('test-package', testDirectory('fixtures/input/test.js'));
    Nova::style('test-package', testDirectory('fixtures/input/test.css'));
    Nova::remoteScript('/input/public.js');
    Nova::remoteStyle('/input/public.css');
});

it('replaces registered styles and scripts with the bundled files', function () {
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'public');
    expect(Nova::$scripts)->toHaveCount(3)->and(Nova::$styles)->toHaveCount(3);

    $middleware = $this->app->make(OverrideNovaPackagesMiddleware::class);
    $middleware->handle(request(), fn () => null);

    expect(Nova::$scripts)->toHaveCount(2)
        ->and(Nova::$styles)->toHaveCount(2)
        ->and(Nova::$scripts[0])->path()->toEqual('https://example.com/index.js')
        ->and(Nova::$scripts[1])->path()->toEqual('http://localhost/vendor/nova-tools/app.js')
        ->and(Nova::$styles[0])->path()->toEqual('https://example.com/app.css')
        ->and(Nova::$styles[1])->path()->toEqual('http://localhost/vendor/nova-tools/app.css');
});

it('replaces registered styles and scripts with the versioned bundled files', function () {
    config()->set('nova-package-bundler-command.version.enabled', true);
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'public');
    expect(Nova::$scripts)->toHaveCount(3)->and(Nova::$styles)->toHaveCount(3);

    file_put_contents(public_path('vendor/nova-tools/manifest.json'), json_encode([
        '/vendor/nova-tools/app.js' => '/vendor/nova-tools/app.js?id=123js',
        '/vendor/nova-tools/app.css' => '/vendor/nova-tools/app.css?id=123css',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    $middleware = $this->app->make(OverrideNovaPackagesMiddleware::class);
    $middleware->handle(request(), fn () => null);

    expect(Nova::$scripts)->toHaveCount(2)
        ->and(Nova::$styles)->toHaveCount(2)
        ->and(Nova::$scripts[0])->path()->toEqual('https://example.com/index.js')
        ->and(Nova::$scripts[1])->path()->toEqual('http://localhost/vendor/nova-tools/app.js?id=123js')
        ->and(Nova::$styles[0])->path()->toEqual('https://example.com/app.css')
        ->and(Nova::$styles[1])->path()->toEqual('http://localhost/vendor/nova-tools/app.css?id=123css');
});

it('keeps url assets that were excluded from the bundle', function () {
    config()->set('nova-package-bundler-command.download_url_assets', true);
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'public');
    expect(Nova::$scripts)->toHaveCount(3)->and(Nova::$styles)->toHaveCount(3);

    $middleware = $this->app->make(OverrideNovaPackagesMiddleware::class);
    $middleware->handle(request(), fn () => null);

    expect(Nova::$scripts)->toHaveCount(1)
        ->and(Nova::$styles)->toHaveCount(1)
        ->and(Nova::$scripts[0])->path()->toEqual('http://localhost/vendor/nova-tools/app.js')
        ->and(Nova::$styles[0])->path()->toEqual('http://localhost/vendor/nova-tools/app.css');
});

it('keeps configured assets that we excluded from the bundle', function () {
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'public');
    expect(Nova::$scripts)->toHaveCount(3)->and(Nova::$styles)->toHaveCount(3);

    Config::set('nova-package-bundler-command.excluded.scripts', ['test-package']);
    Config::set('nova-package-bundler-command.excluded.styles', ['test-package']);

    $middleware = $this->app->make(OverrideNovaPackagesMiddleware::class);
    $middleware->handle(request(), fn () => null);

    expect(Nova::$scripts)->toHaveCount(3)
        ->and(Nova::$styles)->toHaveCount(3)
        ->and(Nova::$scripts[0])->path()->toEqual('https://example.com/index.js')
        ->and(Nova::$scripts[1])->path()->toEqual(testDirectory('fixtures/input/test.js'))
        ->and(Nova::$scripts[2])->path()->toEqual('http://localhost/vendor/nova-tools/app.js')
        ->and(Nova::$styles[0])->path()->toEqual('https://example.com/app.css')
        ->and(Nova::$styles[1])->path()->toEqual(testDirectory('fixtures/input/test.css'))
        ->and(Nova::$styles[2])->path()->toEqual('http://localhost/vendor/nova-tools/app.css');
});

afterEach(function () {
    Nova::$scripts = [];
    Nova::$styles = [];

    @unlink(public_path('/vendor/nova-tools/manifest.json'));
});
