<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts;

use Subscriby\Connector\Data\ConnectorManifest;
use Subscriby\Connector\Exceptions\ConnectorNotRegistered;
use Subscriby\Connector\Exceptions\InvalidManifest;

/**
 * Every connector the application knows, and how to reach each one's ports.
 *
 * Implemented by the application and bound in its container; a connector
 * package's service provider registers into it at boot with the manifest read
 * from the package's `connector.json`. Availability, official status and the
 * disabled list are the application's configuration, never the package's own
 * claim.
 */
interface ConnectorRegistry
{
    /**
     * Accept a connector, checking its manifest against the ports it binds.
     *
     * A manifest that declares install or settings fields and binds no
     * `SettingsSchema` gets the SDK's manifest-backed schema bound for it, so a
     * package whose form is plain data writes no PHP for it.
     *
     * @param  ConnectorManifest  $manifest   What the connector is, as its `connector.json` declares it.
     * @param  Connector          $connector  The connector package's entry point.
     *
     * @throws  InvalidManifest  When the key is taken, a capability lacks its port, a port lacks its capability, a required port is missing, a reserved capability is claimed without being official, or fields are both declared and bound.
     */
    public function register(ConnectorManifest $manifest, Connector $connector): void;

    /**
     * @return  list<string>  Every registered connector key, in registration order.
     */
    public function keys(): array;

    /**
     * @param   string  $key  The connector key.
     * @return  bool    True when a connector is registered under it.
     */
    public function has(string $key): bool;

    /**
     * @param   string             $key  The connector key.
     * @return  ConnectorManifest  Its manifest.
     *
     * @throws  ConnectorNotRegistered  When no connector has that key.
     */
    public function manifest(string $key): ConnectorManifest;

    /**
     * @template T of object
     *
     * @param   string           $key   The connector key.
     * @param   class-string<T>  $port  The port contract.
     * @return  T                The connector's implementation.
     *
     * @throws  ConnectorNotRegistered  When no connector has that key or it does not bind that port.
     */
    public function port(string $key, string $port): object;

    /**
     * @param   string        $key   The connector key.
     * @param   class-string  $port  The port contract.
     * @return  bool          True when the connector binds it.
     */
    public function binds(string $key, string $port): bool;

    /**
     * The connectors a creator may install today.
     *
     * @return  list<string>  Registered keys that configuration lists as available and does not list as disabled.
     */
    public function available(): array;

    /**
     * @param   string  $key  The connector key.
     * @return  bool    True when a creator may install it today.
     */
    public function isAvailable(string $key): bool;

    /**
     * @param   string  $key  The connector key.
     * @return  bool    True when configuration names it an official connector.
     */
    public function isOfficial(string $key): bool;

    /**
     * @param   string  $key  The connector key.
     * @return  bool    True when the kill switch has it off.
     */
    public function isDisabled(string $key): bool;
}
