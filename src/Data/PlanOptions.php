<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a creator picks from before a plan is drafted, so a conversation refuses an answer in place rather than after the save.
 *
 * The currencies are the ones an active payment method of the project
 * supports, because the core refuses any other; the places are the
 * project's resources on sale; the cycles are the core's own.
 */
final readonly class PlanOptions
{
    /**
     * @param  list<PlanOption>  $currencies    The currencies a plan may be priced in: id and ISO code.
     * @param  list<PlanOption>  $resources     The places a plan may grant: id and title.
     * @param  list<PlanOption>  $cycles        The billing cycles: value and label.
     * @param  int               $maxTrialDays  The longest trial the core accepts.
     */
    public function __construct(
        public array $currencies,
        public array $resources,
        public array $cycles,
        public int $maxTrialDays,
    ) {}
}
