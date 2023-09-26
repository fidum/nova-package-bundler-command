<?php

use Fidum\NovaPackageBundler\Tests\Support\TestTool;
use Laravel\Nova\Nova;

use function Pest\Laravel\artisan;
use function Pest\testDirectory;

it('finds and bundles registered scripts and styles', function () {
    $this->app->setBasePath(testDirectory('fixtures'));
    expect(public_path())->toBe('tests/fixtures'.DIRECTORY_SEPARATOR.'public');

    Nova::$tools = [TestTool::make()];
    Nova::serving(function () {
        Nova::script('test-package', testDirectory('fixtures/input/test.js'));
        Nova::style('test-package', testDirectory('fixtures/input/test.css'));
        Nova::remoteScript('/input/public.js');
        Nova::remoteStyle('/input/public.css');
        Nova::remoteScript('https://unpkg.com/is-object@1.0.2/index.js');
        Nova::remoteStyle('https://unpkg.com/tailwindcss@2.2.19/dist/components.min.css');
    });

    artisan('nova:tools:publish')
        ->assertSuccessful()
        ->execute();

    $script = public_path('/vendor/nova-tools/app.js');
    expect($script)
        ->toBeReadableFile()
        ->and(file_get_contents($script))
        ->toMatchSnapshot();

    $style = public_path('/vendor/nova-tools/app.css');
    expect($style)->toBeReadableFile()
        ->and(file_get_contents($style))
        ->toMatchSnapshot();
});

afterAll(function () {
    Nova::$scripts = [];
    Nova::$styles = [];
});
