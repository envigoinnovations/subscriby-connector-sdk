<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * One access code as a creator's surface lists it, masked.
 *
 * A code is a secret shown in full only at generation; afterwards the first
 * and last characters are enough to match a member's ticket, so the core
 * never hands the whole code back.
 */
final readonly class AccessCodeSummary
{
    /**
     * @param  string                  $id          The code's id, what `revoke()` takes.
     * @param  string                  $maskedCode  The code with its middle hidden.
     * @param  bool                    $redeemed    Whether a member redeemed it.
     * @param  DateTimeImmutable|null  $redeemedAt  When, or null.
     * @param  DateTimeImmutable|null  $expiresAt   When it stops being redeemable, or null for never.
     */
    public function __construct(
        public string $id,
        public string $maskedCode,
        public bool $redeemed,
        public ?DateTimeImmutable $redeemedAt,
        public ?DateTimeImmutable $expiresAt,
    ) {}
}
