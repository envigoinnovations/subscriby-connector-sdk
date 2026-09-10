<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * A connector key the registry has never seen, or a port a registered connector does not bind.
 */
final class ConnectorNotRegistered extends RuntimeException
{
    /**
     * @param   string  $key  The connector key that was asked for.
     * @return  self    The exception.
     */
    public static function forKey(string $key): self
    {
        return new self(sprintf('No connector is registered under the key "%s".', $key));
    }

    /**
     * @param   string        $key   The connector key.
     * @param   class-string  $port  The port contract that was asked for.
     * @return  self          The exception.
     */
    public static function forPort(string $key, string $port): self
    {
        return new self(sprintf('Connector "%s" does not bind the port %s.', $key, $port));
    }
}
