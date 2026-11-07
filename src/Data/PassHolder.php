<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\PassHolderStanding;

/**
 * One holder of one date, as the Pass Roster lists them under the date.
 *
 * A holder is a pass: the same member holding a date through two purchases
 * is two holders, each with its own standing, as the dashboard's date
 * dialog lists them. The pass id is what a surface hands back to read the
 * trail.
 */
final readonly class PassHolder
{
    /**
     * @param  string                  $passId           The pass on the date, the ledger row the standing was read from.
     * @param  string                  $subscriptionId   The purchase the pass belongs to.
     * @param  string|null             $memberId         The member, when the purchase still names one.
     * @param  string                  $memberName       The member's name as the dashboard shows it.
     * @param  PassHolderStanding      $standing         Where the holder stands on the date.
     * @param  string                  $standingWord     The dashboard's translated word for it.
     * @param  string|null             $planName         The plan the purchase was made under, or null when the plan is gone.
     * @param  bool                    $fromSeries       Whether the date is held as part of a season rather than bought on its own.
     * @param  string|null             $movedFromDateId  The date the pass last came from, or null when it never moved.
     * @param  string|null             $movedFrom        That date's day label, as the dashboard marks it in the cell, or null.
     * @param  DateTimeImmutable|null  $boughtAt         When the purchase was made.
     * @param  DateTimeImmutable|null  $lastRemindedAt   When the holder was last nudged about the date, or null when never.
     */
    public function __construct(
        public string $passId,
        public string $subscriptionId,
        public ?string $memberId,
        public string $memberName,
        public PassHolderStanding $standing,
        public string $standingWord,
        public ?string $planName,
        public bool $fromSeries,
        public ?string $movedFromDateId,
        public ?string $movedFrom,
        public ?DateTimeImmutable $boughtAt,
        public ?DateTimeImmutable $lastRemindedAt,
    ) {}
}
