<?php

use Fidum\NovaPackageBundler\Tests\Support\TestTool;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Nova\Nova;

use function Pest\Laravel\artisan;
use function Pest\testDirectory;

it('finds and bundles registered scripts and styles', function () {
    $this->app->setBasePath(testDirectory('fixtures'));
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'public');

    Nova::$tools = [TestTool::make()];
    Nova::serving(function () {
        Nova::script('test-package', testDirectory('fixtures/input/test.js'));
        Nova::style('test-package', testDirectory('fixtures/input/test.css'));
        Nova::remoteScript('/input/public.js');
        Nova::remoteStyle('/input/public.css');
        Nova::remoteScript('https://example.com/index.js');
        Nova::remoteStyle('https://example.com/app.css');

        // Files that don't exist are handled
        Nova::script('test-package', testDirectory('fixtures/this-doesnt-exist.js'));
        Nova::style('test-package', testDirectory('fixtures/input/this-doesnt-exist.css'));
        Nova::remoteScript('this-doesnt-exist.js');
        Nova::remoteStyle('this-doesnt-exist.css');
    });

    Http::fake([
        '*.css' => Http::response('/** Remote URL CSS content **/'),
        '*.js' => Http::response('/** Remote URL JS content **/'),
    ]);

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

    Http::assertSentCount(2);
    Http::assertSentInOrder([
        function (Request $request) {
            $this->assertSame('https://example.com/index.js', $request->url());

            return true;
        },
        function (Request $request) {
            $this->assertSame('https://example.com/app.css', $request->url());

            return true;
        },
    ]);
});

afterAll(function () {
    Nova::$scripts = [];
    Nova::$styles = [];
});
