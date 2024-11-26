<?php

use Fidum\NovaPackageBundler\Tests\Support\TestTool;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;
use Laravel\Nova\Nova;

use function Pest\Laravel\artisan;
use function Pest\testDirectory;

beforeEach(function () {
    $this->app->setBasePath(testDirectory('fixtures'));
    Nova::$tools = [TestTool::make()];
    Nova::serving(function () {
        Nova::script('test-package', testDirectory('fixtures/input/test.js'));
        Nova::style('test-package', testDirectory('fixtures/input/test.css'));
        Nova::remoteScript(new HtmlString('/input/public.js'));
        Nova::remoteStyle('/input/public.css');
        Nova::remoteScript('https://example.com/index.js');
        Nova::remoteStyle('https://example.com/app.css');

        // Files that don't exist are handled
        Nova::script('dont-exist-package', testDirectory('fixtures/this-doesnt-exist.js'));
        Nova::style('dont-exist-package', testDirectory('fixtures/input/this-doesnt-exist.css'));
        Nova::remoteScript('this-doesnt-exist.js');
        Nova::remoteStyle('this-doesnt-exist.css');
    });

    Http::fake([
        '*.css' => Http::response('/** Remote URL CSS content **/'),
        '*.js' => Http::response('/** Remote URL JS content **/'),
    ]);
});

it('finds and bundles registered scripts and styles', function () {
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'public');

    artisan('nova:tools:publish')
        ->assertSuccessful()
        ->execute();

    assertFileContent(public_path('/vendor/nova-tools/app.js'));
    assertFileContent(public_path('/vendor/nova-tools/app.css'));
    Http::assertSentCount(0);
});

it('downloads url assets when enabled', function () {
    config()->set('nova-package-bundler-command.download_url_assets', true);
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'public');

    artisan('nova:tools:publish')
        ->assertSuccessful()
        ->execute();

    assertFileContent(public_path('/vendor/nova-tools/app.js'));
    assertFileContent(public_path('/vendor/nova-tools/app.css'));
    assertHttpSent();
});

it('finds and bundles registered scripts and styles and excludes configured assets', function () {
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'public');

    Config::set('nova-package-bundler-command.excluded.scripts', ['tool-boot-package']);
    Config::set('nova-package-bundler-command.excluded.styles', ['tool-boot-package']);

    artisan('nova:tools:publish')
        ->assertSuccessful()
        ->execute();

    assertFileContent(public_path('/vendor/nova-tools/app.js'));
    assertFileContent(public_path('/vendor/nova-tools/app.css'));
    expect(public_path('/vendor/nova-tools/manifest.json'))->not->toBeReadableFile();

    Http::assertSentCount(0);
});

it('finds and bundles registered scripts and styles generates manifest file', function () {
    expect(public_path())->toBe('tests'.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR.'public');

    Config::set('nova-package-bundler-command.version.enabled', true);

    artisan('nova:tools:publish')
        ->assertSuccessful()
        ->execute();

    assertFileContent(public_path('/vendor/nova-tools/app.js'));
    assertFileContent(public_path('/vendor/nova-tools/app.css'));
    assertFileContent(public_path('/vendor/nova-tools/manifest.json'));
    Http::assertSentCount(0);
});

afterEach(function () {
    Nova::$scripts = [];
    Nova::$styles = [];

    @unlink(public_path('/vendor/nova-tools/app.js'));
    @unlink(public_path('/vendor/nova-tools/app.css'));
    @unlink(public_path('/vendor/nova-tools/manifest.json'));
});

function assertFileContent(string $path)
{
    expect($path)
        ->toBeReadableFile()
        ->and(file_get_contents($path))
        ->toMatchSnapshot();
}

function assertHttpSent()
{
    Http::assertSentCount(2);
    Http::assertSentInOrder([
        function (Request $request) {
            expect('https://example.com/index.js')->toBe($request->url());

            return true;
        },
        function (Request $request) {
            expect('https://example.com/app.css')->toBe($request->url());

            return true;
        },
    ]);
}
