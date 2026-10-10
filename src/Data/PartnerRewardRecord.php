<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * One commission or clawback on a partner's ledger, every figure and sentence the core's.
 *
 * The amount is formatted in the programme's currency and a clawback is
 * negative; the moment says what the status means for this row (when it
 * becomes payable, when it was credited, paid or reversed) and the note
 * why, so a connector lists the ledger as the dashboard's Commissions tab
 * does.
 */
final readonly class PartnerRewardRecord
{
    /**
     * @param  string                  $id           The reward row's UUID.
     * @param  DateTimeImmutable|null  $earnedOn     When the row was written.
     * @param  string|null             $creatorName  The referred creator whose invoice earned it; null when the referral is gone.
     * @param  string                  $kindLabel    Commission or clawback, in words.
     * @param  string                  $amount       The amount, formatted; negative on a clawback.
     * @param  string                  $statusLabel  Where the row stands, in words.
     * @param  string                  $moment       What the standing means for this row, with its date.
     * @param  string|null             $note         Why, when the core recorded a reason (a reversal, a recalculation); null otherwise.
     */
    public function __construct(
        public string $id,
        public ?DateTimeImmutable $earnedOn,
        public ?string $creatorName,
        public string $kindLabel,
        public string $amount,
        public string $statusLabel,
        public string $moment,
        public ?string $note,
    ) {}
}
