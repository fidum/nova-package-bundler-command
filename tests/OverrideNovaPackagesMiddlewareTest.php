<?php

use Fidum\NovaPackageBundler\Http\Middleware\OverrideNovaPackagesMiddleware;
use Laravel\Nova\Nova;

use function Pest\testDirectory;

it('replaces registered styles and scripts with the bundled files', function () {
    $this->app->setBasePath(testDirectory('fixtures'));
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtusres'.DIRECTORY_SEPARATOR.'public');

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

afterAll(function () {
    Nova::$scripts = [];
    Nova::$styles = [];
});
