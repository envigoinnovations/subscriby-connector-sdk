<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * A payout the core recorded, with what it covered and what is still owed, for the confirmation a creator reads.
 */
final readonly class ReferralPayoutRecord
{
    /**
     * @param  string             $id                The payout row's UUID.
     * @param  string             $amount            What was paid, formatted.
     * @param  string             $currency          The currency's ISO code.
     * @param  DateTimeImmutable  $paidAt            When it was recorded.
     * @param  int                $rewardsCovered    How many commissions the amount marked paid, oldest first.
     * @param  string             $remainingPayable  What is still payable in that currency after this payout, formatted.
     */
    public function __construct(
        public string $id,
        public string $amount,
        public string $currency,
        public DateTimeImmutable $paidAt,
        public int $rewardsCovered,
        public string $remainingPayable,
    ) {}
}
