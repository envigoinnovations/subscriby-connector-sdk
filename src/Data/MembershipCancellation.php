<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * A membership the member just cancelled.
 */
final readonly class MembershipCancellation
{
    /**
     * @param  string                  $subscriptionId  The subscription's UUID.
     * @param  string                  $planName        The plan it was on.
     * @param  DateTimeImmutable|null  $endsAt          When the access they keep ends, or null when it ended at once.
     */
    public function __construct(
        public string $subscriptionId,
        public string $planName,
        public ?DateTimeImmutable $endsAt,
    ) {}
}
