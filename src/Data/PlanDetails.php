<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\PlanKind;

/**
 * A plan as its creator manages it, in the core's words.
 *
 * The price is a decimal string in the plan's currency and the cadence is
 * the sentence the dashboard prints for it, so a surface shows the plan
 * without pricing arithmetic of its own; the billing fields are there for a
 * draft that edits one of them to start from.
 */
final readonly class PlanDetails
{
    /**
     * @param  string        $id                 The plan's UUID.
     * @param  string        $projectId          The project's UUID.
     * @param  string        $name               The plan's name.
     * @param  string|null   $description        What the plan is, or null.
     * @param  PlanKind      $kind               Paid once, renewing, a pass or a season.
     * @param  string        $price              What one purchase costs, as a decimal string.
     * @param  string        $currency           The ISO code of the plan's currency.
     * @param  string        $cadence            The billing cadence in words, as the dashboard prints it.
     * @param  bool          $active             Whether the plan is on sale.
     * @param  int|null      $salesCap           The sales after which the plan pauses, or null for no limit.
     * @param  int           $salesCapSold       How many sales count towards the cap so far.
     * @param  list<string>  $resourceIds        The places the plan grants, by id.
     * @param  list<string>  $resourceTitles     The same places by title, in the same order.
     * @param  string|null   $billingCycle       The cycle's value for a subscription plan (`day`, `week`, `month`, `year`, `lifetime`), null for a pass.
     * @param  int           $billingCycleCount  How many cycles one purchase buys.
     * @param  bool          $recurring          Whether the plan renews.
     * @param  int           $trialDays          The trial's length, 0 for none.
     * @param  string|null   $pausedReason       Why the plan is off sale when the core paused it, or null.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public string $name,
        public ?string $description,
        public PlanKind $kind,
        public string $price,
        public string $currency,
        public string $cadence,
        public bool $active,
        public ?int $salesCap,
        public int $salesCapSold,
        public array $resourceIds,
        public array $resourceTitles,
        public ?string $billingCycle,
        public int $billingCycleCount,
        public bool $recurring,
        public int $trialDays,
        public ?string $pausedReason = null,
    ) {}
}
