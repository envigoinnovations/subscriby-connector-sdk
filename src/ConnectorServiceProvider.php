<?php

declare(strict_types=1);

namespace Subscriby\Connector;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use Subscriby\Connector\Contracts\Connector;
use Subscriby\Connector\Contracts\ConnectorRegistry;
use Subscriby\Connector\Exceptions\InvalidManifest;
use Subscriby\Connector\Manifest\ManifestFile;

/**
 * The service provider every connector package extends.
 *
 * It does the wiring generically so a package author writes none of it: the
 * package's `connector.json` is read and checked, the connector is registered
 * with the application's registry under that manifest, and the package's
 * migrations, views (namespace `connector-<key>`), JSON translations, inbound
 * routes (under the `connector.inbound` middleware group) and console commands
 * are loaded from the package directory when present, and the listeners the
 * package declares for the SDK's events are bound. The package directory is
 * the grandparent of the concrete provider's file, which is the layout
 * `<package>/src/<Provider>.php` every connector shares.
 */
abstract class ConnectorServiceProvider extends ServiceProvider
{
    /**
     * Read the manifest, register the connector and load what the package ships.
     *
     * @throws  InvalidManifest  When `connector.json` is missing or breaks a rule.
     */
    public function boot(): void
    {
        $manifest = ManifestFile::load($this->packagePath(ManifestFile::FILENAME));
        $key = $manifest->key;

        $this->app->make(ConnectorRegistry::class)->register($manifest, $this->connector());

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

        if (is_file($this->packagePath('routes/web.php'))) {
            Route::middleware('web')->group($this->packagePath('routes/web.php'));
        }

        if ($this->app->runningInConsole() && $this->packageCommands() !== []) {
            $this->commands($this->packageCommands());
        }

        foreach ($this->packageListeners() as $event => $listener) {
            Event::listen($event, $listener);
        }
    }

    /**
     * Load every configuration file the package ships, under the file's own name.
     *
     * A connector's settings are its own (`config/connector-<key>.php`), and a
     * package that wraps a third-party client may carry that client's
     * configuration too, under the key the client reads. Laravel's own merge
     * lets whatever is already in the repository win, which is right for a
     * file the application ships and wrong for the defaults another package's
     * provider merged a moment earlier (the Telegraph fork merges its own
     * `telegraph.php`, webhook path included, before this provider runs). So a
     * key the application owns a file for is merged the Laravel way, and any
     * other key takes the connector's values over what was there. Skipped when
     * the configuration is cached, because the cache was built with these
     * values in place.
     */
    public function register(): void
    {
        if ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached()) {
            return;
        }

        $config = $this->app->make(Repository::class);

        foreach (glob($this->packagePath('config').DIRECTORY_SEPARATOR.'*.php') ?: [] as $file) {
            $name = basename($file, '.php');
            $shipped = require $file;
            $existing = (array) $config->get($name, []);

            $config->set($name, is_file($this->app->configPath($name.'.php'))
                ? array_replace_recursive($shipped, $existing)
                : array_replace_recursive($existing, $shipped));
        }
    }

    /**
     * @return  Connector  The package's entry point: the classes that implement its ports.
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
     * The SDK events the package listens to, if any.
     *
     * The core dispatches `Subscriby\Connector\Events\*` for what it records
     * about a connector's installations, identities and grants; a package that
     * keeps rows of its own in step names the listener here rather than
     * listening to anything under the application's namespace.
     *
     * @return  array<class-string, class-string>  Event class to listener class.
     */
    protected function packageListeners(): array
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
