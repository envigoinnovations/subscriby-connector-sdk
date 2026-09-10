<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use InvalidArgumentException;

/**
 * A message or button that a connector's limits cannot carry.
 *
 * Telegram caps callback data at 64 bytes and a message at 4,096 characters,
 * Discord at 100 and 2,000; checking against the manifest's `MessagingLimits`
 * before sending turns a silent platform refusal into a developer-facing
 * exception with the limit in its text.
 */
final class InvalidMessage extends InvalidArgumentException
{
    /**
     * @param   string  $reason  What is wrong, naming the limit that was exceeded.
     * @return  self    The exception.
     */
    public static function because(string $reason): self
    {
        return new self('The message cannot be sent: '.$reason);
    }
}
