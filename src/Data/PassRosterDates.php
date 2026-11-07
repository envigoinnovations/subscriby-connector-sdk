<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A plan's coming dates, with the plan they belong to.
 *
 * The plan rides along so a screen that lists the dates can head itself
 * and find its way back even when the list is empty.
 */
final readonly class PassRosterDates
{
    /**
     * @param  PassRosterPlan        $plan        The plan.
     * @param  list<PassRosterDate>  $dates       Its dates that have not ended and start within the weeks, soonest first, cancelled ones included.
     * @param  int                   $weeksAhead  How far ahead the list looked.
     */
    public function __construct(
        public PassRosterPlan $plan,
        public array $dates,
        public int $weeksAhead,
    ) {}
}
