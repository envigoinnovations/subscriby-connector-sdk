<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\AccessWindowState;

/**
 * One date a plan sells, as the Pass Roster reads it, with the roll call behind it.
 *
 * The range and the day are printed in the plan's own zone, as the
 * dashboard prints them, so a surface never does date arithmetic of its
 * own; `sold` counts every purchase ever bound to the date, refunded and
 * cancelled ones included, and the other figures explain the gap between
 * it and how many people will walk in.
 */
final readonly class PassRosterDate
{
    /**
     * @param  string             $id          The date's UUID.
     * @param  string             $projectId   The project's UUID.
     * @param  string             $planId      The plan's UUID.
     * @param  string             $planName    The plan's name.
     * @param  DateTimeImmutable  $startsAt    When access begins.
     * @param  DateTimeImmutable  $endsAt      When access ends.
     * @param  string             $timezone    The plan's schedule zone, the one the range is printed in.
     * @param  string             $localRange  The range as the dashboard prints it, such as "Thu 12 Nov 2026, 15:30 – 00:00 CST".
     * @param  string             $dayLabel    The day alone with its weekday, such as "Thu 12 Nov", for a mark that names a date.
     * @param  AccessWindowState  $state       Where the date stands: scheduled, open, closed or cancelled.
     * @param  int                $sold        Every purchase ever bound to the date.
     * @param  int                $active      Holders whose purchase is live and uncancelled.
     * @param  int                $queued      Active holders who tapped their link and wait in the join queue.
     * @param  int                $notQueued   Active holders who have not tapped their link yet.
     * @param  int                $refunded    Holders refunded or cancelled since buying.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public string $planId,
        public string $planName,
        public DateTimeImmutable $startsAt,
        public DateTimeImmutable $endsAt,
        public string $timezone,
        public string $localRange,
        public string $dayLabel,
        public AccessWindowState $state,
        public int $sold,
        public int $active,
        public int $queued,
        public int $notQueued,
        public int $refunded,
    ) {}

    /**
     * @return  bool  True once the creator called the date off.
     */
    public function isCancelled(): bool
    {
        return $this->state === AccessWindowState::Cancelled;
    }
}
