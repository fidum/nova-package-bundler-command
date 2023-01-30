<?php

use Laravel\Nova\Nova;
use function Pest\Laravel\artisan;
use function Pest\testDirectory;
use function Spatie\Snapshots\assertMatchesFileSnapshot;

it('finds and bundles registered scripts and styles', function () {
    $this->app->instance('path.public', testDirectory('fixtures/public'));

    Nova::serving(function () {
        Nova::script('test-package', testDirectory('fixtures/input/test.js'));
        Nova::style('test-package', testDirectory('fixtures/input/test.css'));
        Nova::remoteScript('/input/public.js');
        Nova::remoteStyle('/input/public.css');
    });

    artisan('nova:tools:publish')
        ->assertSuccessful()
        ->execute();

    $script = public_path('/vendor/nova-tools/app.js');
    expect($script)->toBeReadableFile();
    assertMatchesFileSnapshot($script);

    $style = public_path('/vendor/nova-tools/app.css');
    expect($style)->toBeReadableFile();
    assertMatchesFileSnapshot($style);
});

afterAll(function () {
    Nova::$scripts = [];
    Nova::$styles = [];
});
