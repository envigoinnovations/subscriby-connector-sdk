<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\MembershipStatus;

/**
 * An identity's standing in a place, as the platform reports it right now.
 */
final readonly class Membership
{
    /**
     * @param  MembershipStatus        $status  The standing.
     * @param  DateTimeImmutable|null  $since   When it began, when the platform says.
     */
    public function __construct(
        public MembershipStatus $status,
        public ?DateTimeImmutable $since = null,
    ) {}
}
