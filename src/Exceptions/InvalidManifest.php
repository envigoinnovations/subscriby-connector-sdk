<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use InvalidArgumentException;

/**
 * A manifest that contradicts itself or the SDK's rules.
 *
 * Raised while the manifest is built, so a connector with a bad manifest fails
 * at boot on the developer's machine rather than at the moment a creator opens
 * the directory.
 */
final class InvalidManifest extends InvalidArgumentException
{
    /**
     * @param   string  $key     The connector key, or what was offered as one.
     * @param   string  $reason  What is wrong, in one sentence.
     * @return  self    The exception.
     */
    public static function because(string $key, string $reason): self
    {
        return new self(sprintf('The manifest of connector "%s" is invalid: %s', $key, $reason));
    }
}
