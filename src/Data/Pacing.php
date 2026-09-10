<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Exceptions\InvalidManifest;

/**
 * How fast the core may send through a connector.
 *
 * Telegram tolerates about thirty messages a second, Slack about one per
 * channel; a broadcast job that paces at a fixed number is right for one
 * platform and either wasteful or banned on the next. The connector declares
 * its pacing and the core's rate limiter reads it.
 */
final readonly class Pacing
{
    /**
     * @param  int  $minIntervalMicroseconds           Gap between two sends on the connector as a whole.
     * @param  int  $burst                             Sends allowed back to back before the gap applies.
     * @param  int  $perRecipientIntervalMicroseconds  Gap between two sends to the same recipient.
     *
     * @throws  InvalidManifest  When a value is negative.
     */
    public function __construct(
        public int $minIntervalMicroseconds,
        public int $burst,
        public int $perRecipientIntervalMicroseconds,
    ) {
        if ($minIntervalMicroseconds < 0 || $burst < 0 || $perRecipientIntervalMicroseconds < 0) {
            throw InvalidManifest::because('unknown', 'pacing values cannot be negative');
        }
    }
}
