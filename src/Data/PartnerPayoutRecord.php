<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * One cash payout the owner recorded for a partner, with the reference the transfer carried.
 */
final readonly class PartnerPayoutRecord
{
    /**
     * @param  string                  $id           The payout row's UUID.
     * @param  DateTimeImmutable|null  $paidAt       When it was paid.
     * @param  string                  $amount       The amount, formatted.
     * @param  string                  $methodLabel  The rail it went out on, in words.
     * @param  string|null             $reference    The transfer's reference; null when none was recorded.
     * @param  string|null             $note         The owner's note; null when none.
     */
    public function __construct(
        public string $id,
        public ?DateTimeImmutable $paidAt,
        public string $amount,
        public string $methodLabel,
        public ?string $reference,
        public ?string $note,
    ) {}
}
