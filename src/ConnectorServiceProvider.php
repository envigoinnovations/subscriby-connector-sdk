<?php

declare(strict_types=1);

namespace Subscriby\Connector;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use Subscriby\Connector\Contracts\Connector;
use Subscriby\Connector\Contracts\ConnectorRegistry;

/**
 * The service provider every connector package extends.
 *
 * It does the wiring generically so a package author writes none of it: the
 * connector is registered with the application's registry, the package's
 * migrations, views (namespace `connector-<key>`), JSON translations, inbound
 * routes (under the `connector.inbound` middleware group) and console commands
 * are loaded from the package directory when present. The package directory is
 * the grandparent of the concrete provider's file, which is the layout
 * `<package>/src/<Provider>.php` every connector shares.
 */
abstract class ConnectorServiceProvider extends ServiceProvider
{
    /**
     * Register the connector and load what the package ships.
     */
    public function boot(): void
    {
        $connector = $this->connector();
        $key = $connector->manifest()->key;

        $this->app->make(ConnectorRegistry::class)->register($connector);

        if (is_dir($this->packagePath('database/migrations'))) {
            $this->loadMigrationsFrom($this->packagePath('database/migrations'));
        }

        if (is_dir($this->packagePath('resources/views'))) {
            $this->loadViewsFrom($this->packagePath('resources/views'), 'connector-'.$key);
        }

        if (is_dir($this->packagePath('lang'))) {
            $this->loadJsonTranslationsFrom($this->packagePath('lang'));
        }

        if (is_file($this->packagePath('routes/inbound.php'))) {
            Route::middleware('connector.inbound')->group($this->packagePath('routes/inbound.php'));
        }

        if ($this->app->runningInConsole() && $this->packageCommands() !== []) {
            $this->commands($this->packageCommands());
        }
    }

    /**
     * @return  Connector  The package's entry point.
     */
    abstract protected function connector(): Connector;

    /**
     * The console commands the package ships, if any.
     *
     * Named apart from Laravel's own `ServiceProvider::commands()`, which is the
     * registration call this hook feeds.
     *
     * @return  list<class-string>  Command classes to register.
     */
    protected function packageCommands(): array
    {
        return [];
    }

    /**
     * @param   string  $path  A path inside the package, without a leading slash.
     * @return  string  The absolute path.
     */
    protected function packagePath(string $path = ''): string
    {
        $file = (string) (new ReflectionClass(static::class))->getFileName();
        $root = dirname($file, 2);

        return $path === '' ? $root : $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
    }
}
