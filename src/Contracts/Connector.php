<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts;

/**
 * A connector package's entry point: the ports it implements.
 *
 * What the connector is lives in the package's `connector.json`, which the
 * SDK's service provider reads and hands to the registry; this class only says
 * which classes prove it. The registry checks the two against each other when
 * the connector boots, so a capability without its port, or a port without its
 * capability, fails on the developer's machine.
 */
interface Connector
{
    /**
     * Bind the port implementations this connector provides.
     *
     * @param  ConnectorRegistrar  $registrar  Receives one implementation per port contract.
     */
    public function register(ConnectorRegistrar $registrar): void;
}
