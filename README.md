# Improves Laravel Nova initial load speeds by combining all third party package assets into a single file.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fidum/nova-package-bundler-command.svg?style=for-the-badge)](https://packagist.org/packages/fidum/nova-package-bundler-command)
[![GitHub Workflow Status (with branch)](https://img.shields.io/github/actions/workflow/status/fidum/nova-package-bundler-command/run-tests.yml?branch=main&style=for-the-badge)](https://github.com/fidum/nova-package-bundler-command/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Twitter Follow](https://img.shields.io/badge/follow-%40danmasonmp-1DA1F2?logo=twitter&style=for-the-badge)](https://twitter.com/danmasonmp)

## Installation

You can install the package via composer:

```bash
composer require fidum/nova-package-bundler-command
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="nova-package-bundler-command-config"
```

[Click here to see the contents of the config file](config/nova-package-bundler-command.php).

You should read through the config, which serves as additional documentation and make changes as needed.

Update Nova configuration file in `config/nova.php`. Add the `OverrideNovaPackagesMiddleware` to the `middleware` option after `BootTools`:

```php
use Fidum\NovaPackageBundler\Http\Middleware\OverrideNovaPackagesMiddleware;
use Laravel\Nova\Http\Middleware\BootTools;
use Laravel\Nova\Http\Middleware\DispatchServingNovaEvent;
use Laravel\Nova\Http\Middleware\HandleInertiaRequests;

return [

    // ...

    'middleware' => [
        'web',
        HandleInertiaRequests::class,
        DispatchServingNovaEvent::class,
        BootTools::class,
        OverrideNovaPackagesMiddleware::class
    ],

    // ...
];
```

## Usage

Run the below command whenever you upgrade your third party nova packages. This should output the files configured above, you should commit the files to your repo. 

```console
$ php artisan nova:tools:publish 

Booting tool [App\Nova\Tools\HelpLink] .................................................................................................. 0ms DONE
Booting tool [App\Nova\Tools\QuickQuote] ................................................................................................ 0ms DONE

Reading asset [1feb8c78f6bd6ba8a6a29cab353ebd8d] from [public/vendor/nova-kit/nova-packages-tool/tool.js] ............................... 0ms DONE
Reading asset [nova-apex-chart] from [vendor/coroowicaksono/chart-js-integration/src/../dist/js/chart-js-integration.js] ................ 3ms DONE
Reading asset [multiselect-field] from [vendor/outl1ne/nova-multiselect-field/src/../dist/js/entry.js] .................................. 2ms DONE
Reading asset [nova-multiselect-filter] from [vendor/outl1ne/nova-multiselect-filter/src/../dist/js/entry.js] ........................... 2ms DONE
Reading asset [nova-opening-hours-field] from [vendor/sadekd/nova-opening-hours-field/src/../dist/js/field.js] .......................... 1ms DONE
Reading asset [nova-tag-input] from [vendor/superlatif/nova-tag-input/src/../dist/js/field.js] .......................................... 2ms DONE
Writing file [public/vendor/nova-tools/app.js] .......................................................................................... 1ms DONE

Reading asset [multiselect-field] from [vendor/outl1ne/nova-multiselect-field/src/../dist/css/entry.css] ................................ 0ms DONE
Reading asset [nova-multiselect-filter] from [vendor/outl1ne/nova-multiselect-filter/src/../dist/css/entry.css] ......................... 0ms DONE
Reading asset [nova-opening-hours-field] from [vendor/sadekd/nova-opening-hours-field/src/../dist/css/field.css] ........................ 0ms DONE
Reading asset [nova-tag-input] from [vendor/superlatif/nova-tag-input/src/../dist/css/field.css] ........................................ 0ms DONE
Writing file [public/vendor/nova-tools/app.css] ......................................................................................... 0ms DONE
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/dmason30/.github/blob/main/CONTRIBUTING.md) for details.

## Credits

- [Dan Mason](https://github.com/dmason30)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
