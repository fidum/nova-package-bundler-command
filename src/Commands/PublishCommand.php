<?php

declare(strict_types=1);

namespace Fidum\NovaPackageBundler\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Asset;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class PublishCommand extends Command
{
    public $signature = 'nova:tools:publish';

    public $description = 'Combines nova styles and scripts into single asset files';

    public function handle(Filesystem $files): int
    {
        ServingNova::dispatch(new Request());
        $this->bootTools();

        foreach ($this->methods() as $method => [$type, $outputPath]) {
            $content = '';

            /** @var Asset $file */
            foreach (Nova::{$method}() as $file) {
                $name = $file->name();
                $path = $file->path();

                if ($file->isRemote() && ! Str::startsWith($path, ['http://', 'https://', '://'])) {
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
                $this->components->task("Writing file [$outputPath]", function () use ($files, $outputPath, $content) {
                    $files->ensureDirectoryExists(dirname($outputPath));
                    $files->put($outputPath, $content);
                });
                $this->line('');
            }
        }

        return static::SUCCESS;
    }

    private function methods(): array
    {
        return [
            'allScripts' => ['js', public_path(config('nova-package-bundler-command.paths.script'))],
            'allStyles' => ['css', public_path(config('nova-package-bundler-command.paths.style'))],
        ];
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
        $result = @file_get_contents($path, false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
            'http' => [
                'timeout' => 5,
            ],
        ]));

        if (is_string($result)) {
            return $result;
        }

        return null;
    }
}
