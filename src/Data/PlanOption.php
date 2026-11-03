<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One thing a creator may pick while drafting a plan, by the value the draft takes and the words a screen shows.
 *
 * The same shape serves a currency (its id and ISO code), a place the plan
 * grants (its id and title) and a billing cycle (its value and label), so a
 * connector renders one kind of picker and hands the value straight back.
 */
final readonly class PlanOption
{
    /**
     * @param  string  $value  What the draft takes: a UUID, or a cycle's value.
     * @param  string  $label  What a creator reads: an ISO code, a place's title, a cycle's name.
     */
    public function __construct(
        public string $value,
        public string $label,
    ) {}
}
