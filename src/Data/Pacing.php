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
 * its pacing, the core's registry registers a `connector:{key}` rate limiter
 * from it, and every queued send the core paces names that limiter.
 */
final readonly class Pacing
{
    /** The prefix of the rate limiter each connector's pacing registers. */
    public const string LIMITER_PREFIX = 'connector:';

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

    /**
     * @param   string  $connector  The connector key.
     * @return  string  The rate limiter a queued send through that connector names.
     */
    public static function limiterName(string $connector): string
    {
        return self::LIMITER_PREFIX.$connector;
    }

    /**
     * How many sends a second the connector tolerates, as a whole-number rate limit.
     *
     * The gap between sends caps the rate, and the burst caps how many may go
     * out inside one second before the gap applies; the limiter takes the lower
     * of the two and never less than one, so a platform that allows a message a
     * second is paced at one and a 35 ms platform at twenty-eight.
     *
     * @return  int  Sends per second, at least one.
     */
    public function sendsPerSecond(): int
    {
        $byInterval = intdiv(1_000_000, max(1, $this->minIntervalMicroseconds));

        return max(1, min($this->burst, $byInterval));
    }
}
