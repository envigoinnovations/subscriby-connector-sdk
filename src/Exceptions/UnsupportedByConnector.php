<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use LogicException;

/**
 * A facet of a port the connector's manifest does not declare.
 *
 * `RecoverySupport` bundles five facets in one port because they are one
 * subsystem; a connector that only probes health implements the rest by
 * throwing this, and the core never calls a facet the manifest denies. Reaching
 * it is therefore a programming error in the core, not a runtime condition.
 */
final class UnsupportedByConnector extends LogicException
{
    /**
     * @param   string  $connector  The connector key.
     * @param   string  $facet      What was asked for.
     * @return  self    The exception.
     */
    public static function facet(string $connector, string $facet): self
    {
        return new self(sprintf('Connector "%s" does not support %s; check the manifest before calling it.', $connector, $facet));
    }
}
