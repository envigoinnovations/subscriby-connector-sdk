<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The fields a creator's surface sends to create or change a subscription plan, field by field.
 *
 * A field the draft never names keeps its stored value (or the core's
 * default on a create), so a conversation that changes the price sends the
 * price; the places replace the current list when named. A create needs
 * the name, the price, the currency, the cycle and at least one place; the
 * core validates the rest and refuses what it would refuse on the
 * dashboard. Passes and seasons keep their dated schedules on the dashboard,
 * so a draft never names them.
 */
final readonly class PlanDraft
{
    /** The plan's name, 5 to 255 characters, unique in the project. */
    public const string NAME = 'name';

    /** What the plan is, up to 1000 characters; null to clear. */
    public const string DESCRIPTION = 'description';

    /** What one purchase costs, as a decimal string; `0` for a free plan where the tier allows it. */
    public const string PRICE = 'price';

    /** The currency, by id, from `PlanOptions::$currencies`. */
    public const string CURRENCY_ID = 'currency_id';

    /** The billing cycle's value, from `PlanOptions::$cycles`. */
    public const string BILLING_CYCLE = 'billing_cycle';

    /** How many cycles one purchase buys, 1 to 99; 1 for a lifetime plan. */
    public const string BILLING_CYCLE_COUNT = 'billing_cycle_count';

    /** Whether the plan renews. */
    public const string RECURRING = 'recurring';

    /** The trial's length in days, 0 for none. */
    public const string TRIAL_DAYS = 'trial_days';

    /** The places the plan grants, by id, from `PlanOptions::$resources`; the whole list. */
    public const string RESOURCE_IDS = 'resources';

    /** Whether the plan is on sale. */
    public const string ACTIVE = 'active';

    /** The sales after which the plan pauses, 1 to 100000; null for no limit. */
    public const string SALES_CAP = 'sales_cap';

    /** Only members who never held a membership may buy; exclusive with the two below. */
    public const string NEWCOMERS_ONLY = 'newcomers_only';

    /** Only members holding a live membership may buy. */
    public const string CUSTOMERS_ONLY = 'customers_only';

    /** Only members who held one and left may buy. */
    public const string CHURNED_ONLY = 'churned_only';

    /** Each member may buy once. */
    public const string SINGLE_USE = 'single_use';

    /** The plan is bought with an access code only. */
    public const string ACCESS_CODES_ONLY = 'access_codes_only';

    /**
     * @param  array<string, mixed>  $fields  The fields named so far, by the constants above.
     */
    public function __construct(
        public array $fields = [],
    ) {}

    /**
     * @param   string  $field  One of the constants above.
     * @param   mixed   $value  Its value.
     * @return  self    A draft naming this field too.
     */
    public function with(string $field, mixed $value): self
    {
        return new self([...$this->fields, $field => $value]);
    }

    /**
     * @param   string  $field  One of the constants above.
     * @return  bool    Whether the draft names it.
     */
    public function has(string $field): bool
    {
        return array_key_exists($field, $this->fields);
    }
}
