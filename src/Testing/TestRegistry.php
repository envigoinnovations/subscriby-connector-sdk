<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing;

use Illuminate\Contracts\Translation\Translator;
use Subscriby\Connector\Contracts\Connector;
use Subscriby\Connector\Contracts\ConnectorRegistry;
use Subscriby\Connector\Data\ConnectorManifest;
use Subscriby\Connector\Exceptions\ConnectorNotRegistered;
use Subscriby\Connector\Exceptions\InvalidManifest;
use Subscriby\Connector\Registry\PortAgreement;

/**
 * The registry a connector's own test suite hands the conformance kit, with no application behind it.
 *
 * It accepts a connector exactly as the application's registry does, through
 * the SDK's `PortAgreement`, so a package the kit passes here is one Subscriby
 * boots, and one refused here fails with the words production would use.
 * Availability, the official list and the kill switch are constructor
 * arguments, because in the application they are configuration and never a
 * package's own claim; by default every registered connector is available,
 * none is official and none is disabled. Manifest-declared forms come back
 * through the passthrough translator, so labels read as the file wrote them.
 */
final class TestRegistry implements ConnectorRegistry
{
    /** @var array<string, Connector> */
    private array $connectors = [];

    /** @var array<string, ConnectorManifest> */
    private array $manifests = [];

    /** @var array<string, array<class-string, object>> */
    private array $ports = [];

    /** @var array<string, list<class-string>> */
    private array $seeders = [];

    /**
     * @param  list<string>       $official    Keys treated as official, which alone may declare native payments.
     * @param  list<string>|null  $available   Keys a creator may install, or null for every registered key.
     * @param  list<string>       $disabled    Keys the kill switch has off.
     * @param  Translator         $translator  Translates manifest-declared form labels; the passthrough by default.
     */
    public function __construct(
        private readonly array $official = [],
        private readonly ?array $available = null,
        private readonly array $disabled = [],
        private readonly Translator $translator = new PassthroughTranslator,
    ) {}

    /**
     * Accept a connector, checking its manifest against the ports it binds.
     *
     * @param  ConnectorManifest  $manifest   What the connector is, as its `connector.json` declares it.
     * @param  Connector          $connector  The connector package's entry point.
     *
     * @throws  InvalidManifest  When the key is taken, the ports and the manifest disagree, or fields are both declared and bound.
     */
    public function register(ConnectorManifest $manifest, Connector $connector): void
    {
        if (isset($this->connectors[$manifest->key])) {
            throw InvalidManifest::because($manifest->key, 'a connector with this key is already registered');
        }

        $this->ports[$manifest->key] = PortAgreement::collect($manifest, $connector, $this->translator, $this->isOfficial($manifest->key));
        $this->connectors[$manifest->key] = $connector;
        $this->manifests[$manifest->key] = $manifest;
    }

    /**
     * @return  list<string>  Every registered key, in registration order.
     */
    public function keys(): array
    {
        return array_keys($this->connectors);
    }

    /**
     * @param   string  $key  The connector key.
     * @return  bool    True when registered.
     */
    public function has(string $key): bool
    {
        return isset($this->connectors[$key]);
    }

    /**
     * @param   string             $key  The connector key.
     * @return  ConnectorManifest  Its manifest.
     *
     * @throws  ConnectorNotRegistered  When no connector has that key.
     */
    public function manifest(string $key): ConnectorManifest
    {
        return $this->manifests[$key] ?? throw ConnectorNotRegistered::forKey($key);
    }

    /**
     * @template T of object
     *
     * @param   string           $key   The connector key.
     * @param   class-string<T>  $port  The port contract.
     * @return  T                The implementation.
     *
     * @throws  ConnectorNotRegistered  When no connector has that key or it does not bind that port.
     */
    public function port(string $key, string $port): object
    {
        if (! isset($this->connectors[$key])) {
            throw ConnectorNotRegistered::forKey($key);
        }

        $implementation = $this->ports[$key][$port] ?? throw ConnectorNotRegistered::forPort($key, $port);

        return $implementation instanceof $port ? $implementation : throw ConnectorNotRegistered::forPort($key, $port);
    }

    /**
     * @param   string        $key   The connector key.
     * @param   class-string  $port  The port contract.
     * @return  bool          True when the connector binds it.
     */
    public function binds(string $key, string $port): bool
    {
        return isset($this->ports[$key][$port]);
    }

    /**
     * @return  list<string>  Registered keys the constructor lists as available, or every one when it listed none, less the disabled.
     */
    public function available(): array
    {
        return array_values(array_filter($this->keys(), fn (string $key): bool => $this->isAvailable($key)));
    }

    /**
     * @param   string  $key  The connector key.
     * @return  bool    True when registered, listed as available (or nothing was listed) and not disabled.
     */
    public function isAvailable(string $key): bool
    {
        return $this->has($key)
            && ($this->available === null || in_array($key, $this->available, true))
            && ! $this->isDisabled($key);
    }

    /**
     * @param   string  $key  The connector key.
     * @return  bool    True when the constructor named it official.
     */
    public function isOfficial(string $key): bool
    {
        return in_array($key, $this->official, true);
    }

    /**
     * @param   string  $key  The connector key.
     * @return  bool    True when the constructor listed it as disabled.
     */
    public function isDisabled(string $key): bool
    {
        return in_array($key, $this->disabled, true);
    }

    /**
     * @param  string              $key      The connector key.
     * @param  list<class-string>  $seeders  Seeder classes, in the order they run.
     */
    public function registerSeeders(string $key, array $seeders): void
    {
        $this->seeders[$key] = array_values($seeders);
    }

    /**
     * @return  list<class-string>  Every registered connector's seeders, in registration order.
     */
    public function seeders(): array
    {
        return array_merge([], ...array_values($this->seeders));
    }
}
