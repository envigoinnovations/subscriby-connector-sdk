<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts;

use Subscriby\Connector\Data\ConnectorManifest;

/**
 * A connector package's entry point.
 *
 * The manifest says what the connector is and does; `register()` binds the
 * ports that prove it. The registry checks the two against each other when the
 * connector boots, so a capability without its port, or a port without its
 * capability, fails on the developer's machine.
 */
interface Connector
{
    /**
     * @return  ConnectorManifest  Everything the core knows about the connector without running it.
     */
    public function manifest(): ConnectorManifest;

    /**
     * Bind the port implementations this connector provides.
     *
     * @param  ConnectorRegistrar  $registrar  Receives one implementation per port contract.
     */
    public function register(ConnectorRegistrar $registrar): void;
}
