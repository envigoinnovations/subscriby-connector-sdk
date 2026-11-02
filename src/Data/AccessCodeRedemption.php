<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * The membership an access code opened.
 */
final readonly class AccessCodeRedemption
{
    /**
     * @param  string                  $subscriptionId  The subscription's UUID.
     * @param  string                  $planName        The plan the code stood for.
     * @param  DateTimeImmutable|null  $endsAt          When the access it opened ends, when a date is known.
     */
    public function __construct(
        public string $subscriptionId,
        public string $planName,
        public ?DateTimeImmutable $endsAt,
    ) {}
}
