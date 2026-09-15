<?php

declare(strict_types=1);

namespace Subscriby\Connector\Registry;

use InvalidArgumentException;
use Subscriby\Connector\Contracts\ConnectorRegistrar;

/**
 * Collects the ports one connector binds while it registers.
 *
 * A fresh registrar is handed to each connector so a port bound by one can
 * never leak into another, and the implementation is checked against the
 * contract at bind time rather than at the first call in production. Every
 * registry, the application's and the kit's, collects through this class, so
 * a connector is handed the same object whichever one accepts it.
 */
final class PortRegistrar implements ConnectorRegistrar
{
    /** @var array<class-string, object> */
    private array $ports = [];

    /**
     * @template T of object
     *
     * @param  class-string<T>  $port            The port contract.
     * @param  T                $implementation  The connector's implementation.
     *
     * @throws  InvalidArgumentException  When the contract is not an interface, the implementation does not honour it, or the port was already bound.
     */
    public function port(string $port, object $implementation): void
    {
        if (! interface_exists($port)) {
            throw new InvalidArgumentException(sprintf('%s is not a port contract.', $port));
        }

        if (! $implementation instanceof $port) {
            throw new InvalidArgumentException(sprintf('%s does not implement %s.', $implementation::class, $port));
        }

        if (isset($this->ports[$port])) {
            throw new InvalidArgumentException(sprintf('%s was bound twice.', $port));
        }

        $this->ports[$port] = $implementation;
    }

    /**
     * @return  array<class-string, object>  Everything bound, keyed by contract.
     */
    public function ports(): array
    {
        return $this->ports;
    }
}
