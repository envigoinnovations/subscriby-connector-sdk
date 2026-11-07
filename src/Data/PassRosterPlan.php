<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\PlanKind;

/**
 * A plan that sells dates, as the Pass Roster's first screen lists it.
 */
final readonly class PassRosterPlan
{
    /**
     * @param  string    $id          The plan's UUID.
     * @param  string    $projectId   The project's UUID.
     * @param  string    $name        The plan's name.
     * @param  PlanKind  $kind        A pass or a season.
     * @param  int       $datesAhead  How many of its dates are still to come, cancelled ones left out.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public string $name,
        public PlanKind $kind,
        public int $datesAhead,
    ) {}
}
