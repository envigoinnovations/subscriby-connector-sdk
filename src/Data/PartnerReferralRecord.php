<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * One creator a partner referred, as the partner reads them: the display name, never the address.
 *
 * The standing and the outcome are the core's sentences, so a connector
 * prints what the dashboard's Referrals tab prints for the same row.
 */
final readonly class PartnerReferralRecord
{
    /**
     * @param  string                  $id                 The referral row's UUID.
     * @param  string                  $creatorName        The referred creator's display name.
     * @param  string                  $standingLabel      Where the referral stands, in words.
     * @param  string                  $outcome            What happened or will: paid on a date, the window closing on a date, held for review, rejected, expired.
     * @param  DateTimeImmutable|null  $signedUpAt         When they signed up.
     * @param  DateTimeImmutable|null  $earnsUntil         Until when their invoices earn the partner a commission; null before conversion.
     * @param  string|null             $commissionPercent  The rate locked onto the referral, two decimals; null before conversion.
     */
    public function __construct(
        public string $id,
        public string $creatorName,
        public string $standingLabel,
        public string $outcome,
        public ?DateTimeImmutable $signedUpAt,
        public ?DateTimeImmutable $earnsUntil,
        public ?string $commissionPercent,
    ) {}
}
