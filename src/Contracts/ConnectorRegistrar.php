<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts;

/**
 * What a connector binds its ports into while registering.
 */
interface ConnectorRegistrar
{
    /**
     * Bind one port implementation.
     *
     * @template T of object
     *
     * @param  class-string<T>  $port            The port contract under `Subscriby\Connector\Contracts\Ports`.
     * @param  T                $implementation  The connector's implementation of it.
     */
    public function port(string $port, object $implementation): void;
}
