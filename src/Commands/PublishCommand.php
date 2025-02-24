<?php

declare(strict_types=1);

namespace Fidum\NovaPackageBundler\Commands;

use Fidum\NovaPackageBundler\Concerns\IdentifiesUrls;
use Fidum\NovaPackageBundler\Contracts\Services\AssetService;
use Fidum\NovaPackageBundler\Contracts\Services\ManifestBuilderService;
use Fidum\NovaPackageBundler\Contracts\Services\ScriptAssetService;
use Fidum\NovaPackageBundler\Contracts\Services\StyleAssetService;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Nova\Asset;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class PublishCommand extends Command
{
    use IdentifiesUrls;

    public $signature = 'nova:tools:publish';

    public $description = 'Combines nova styles and scripts into single asset files';

    public function handle(
        Filesystem $filesystem,
        ScriptAssetService $scriptAssetService,
        StyleAssetService $styleAssetService,
        ManifestBuilderService $manifestService,
    ): int {
        $reflection = new \ReflectionMethod(ServingNova::class, '__construct');

        if ($reflection->getNumberOfParameters() === 1) {
            ServingNova::dispatch(new Request);
        } else {
            /** @var \Illuminate\Contracts\Foundation\Application $app */
            $app = Container::getInstance();

            ServingNova::dispatch($app, new Request);
        }

        $this->bootTools();
        $this->process($filesystem, $scriptAssetService, $manifestService);
        $this->process($filesystem, $styleAssetService, $manifestService);
        $this->createManifestFile($filesystem, $manifestService);

        return static::SUCCESS;
    }

    private function process(
        Filesystem $filesystem,
        AssetService $assetService,
        ManifestBuilderService $manifestService,
    ): void {
        $content = '';

        /** @var Asset $asset */
        foreach ($assetService->allowed() as $asset) {
            $name = $asset->name();
            $path = (string) $asset->path();

            if ($asset->isRemote() && ! $this->isUrl($path)) {
                $path = public_path($path);
            }

            $this->components->task("Reading asset [$name] from [$path]", function () use (&$content, $path) {
                $result = $this->readFile($path);

                if ($result) {
                    $content .= trim($result).PHP_EOL;

                    return true;
                }

                return file_exists($path);
            });
        }

        if ($content) {
            $outputPath = $assetService->getLocalOutputPath();
            $outputPublicPath = public_path($outputPath);
            $this->components->task("Writing file [$outputPublicPath]", function () use ($filesystem, $outputPublicPath, $content) {
                $filesystem->ensureDirectoryExists(dirname($outputPublicPath));
                $filesystem->put($outputPublicPath, $content);
            });
            $this->line('');

            $manifestService->push($outputPath, $content);
        }
    }

    private function createManifestFile(
        Filesystem $filesystem,
        ManifestBuilderService $service
    ): void {
        if (! $service->enabled()) {
            return;
        }

        $content = $service->json();
        $outputPath = public_path($service->manifestPath());

        $this->components->task("Writing file [$outputPath]", function () use ($filesystem, $outputPath, $content) {
            $filesystem->ensureDirectoryExists(dirname($outputPath));
            $filesystem->put($outputPath, $content);
        });
    }

    private function bootTools(): void
    {
        if (Nova::$tools) {
            foreach (Nova::$tools as $tool) {
                $name = $tool::class;
                $this->components->task("Booting tool [$name]", function () use ($tool) {
                    try {
                        $tool->boot();
                    } catch (\Throwable $e) {
                        // Do nothing
                    }
                });
            }

            $this->line('');
        }
    }

    private function readFile(string $path): ?string
    {
        $result = match ($this->isUrl($path)) {
            true => Http::withoutVerifying()->get($path)->body(),
            default => @file_get_contents($path)
        };

        if (is_string($result)) {
            return $result;
        }

        return null;
    }
}
