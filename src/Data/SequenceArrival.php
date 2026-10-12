<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a member who opened a project bot through a sequence step's button is shown.
 *
 * The step's deep link names the member's passage; the core answers the
 * plan the passage is about, with the step's coupon already recorded for
 * the member on it, so the connector only has to open the plan and the
 * checkout applies the discount under the coupon's own rules.
 */
final readonly class SequenceArrival
{
    /**
     * @param  string|null  $planId      The plan the step's button reopens, or null when the passage is about none; the connector shows its home then.
     * @param  string|null  $couponCode  The coupon the step carried, recorded for the member on that plan, or null when the step carried none.
     */
    public function __construct(
        public ?string $planId,
        public ?string $couponCode,
    ) {}
}
