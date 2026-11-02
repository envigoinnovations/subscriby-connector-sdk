<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\PlanKind;

/**
 * One plan a project offers, as a connector lists it with the way to buy it.
 */
final readonly class PlanOffer
{
    /**
     * @param  string       $id           The plan's UUID.
     * @param  string       $name         The plan's name.
     * @param  string|null  $description  The plan's description in the canonical HTML subset, or null when it has none.
     * @param  PlanKind     $kind         What kind of plan it is.
     * @param  string       $price        The price, formatted in the plan's currency.
     * @param  string|null  $cycle        The billing cycle for a recurring plan, translated, or null for a plan paid once.
     * @param  int          $trialDays    The trial's length, zero when there is none.
     * @param  int|null     $seatsLeft    How many may still buy, or null when the plan has no cap.
     * @param  string|null  $checkoutUrl  The portal checkout that opens this plan, or null while the project has no handle.
     * @param  bool         $scheduled    Whether the plan is a pass on a schedule, whose next dates the portal shows.
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public PlanKind $kind,
        public string $price,
        public ?string $cycle,
        public int $trialDays,
        public ?int $seatsLeft,
        public ?string $checkoutUrl,
        public bool $scheduled = false,
    ) {}
}
