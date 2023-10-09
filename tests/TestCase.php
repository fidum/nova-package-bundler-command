<?php

namespace Fidum\NovaPackageBundler\Tests;

use Fidum\NovaPackageBundler\NovaPackageBundlerServiceProvider;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    protected function getPackageProviders($app)
    {
        return [
            NovaPackageBundlerServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_nova-package-bundler-command_table.php.stub';
        $migration->up();
        */
    }
}
