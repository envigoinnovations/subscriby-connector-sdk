<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The settings a creator's surface sends to save a Referral Program, field by field.
 *
 * Absent and null are different things here: a field the draft never names
 * keeps its stored value (or the core's default on a first save), while a
 * field set to null is cleared, which is how the terms, the payout question
 * or a friend coupon are removed. A draft therefore grows one field at a
 * time through {@see with()} and only names what the conversation asked;
 * the core fills the rest and refuses a shape that does not add up (a cash
 * programme without a rate, a friend coupon the project does not own), so
 * the cross-field rules live in one place for every surface.
 */
final readonly class ReferralProgramDraft
{
    /** Free days or cash: a `ReferralRewardKind` value. */
    public const string REWARD_KIND = 'reward_kind';

    /** Days a referrer earns per converted friend, 1 to the options' maximum. */
    public const string REWARD_DAYS = 'reward_days';

    /** A `ReferralCommissionType` value, on a cash programme. */
    public const string COMMISSION_TYPE = 'commission_type';

    /** The rate (up to 100 for a percentage) or the fixed amount, as a decimal string. */
    public const string COMMISSION_VALUE = 'commission_value';

    /** The currency a fixed commission is paid in, by id. */
    public const string COMMISSION_CURRENCY_ID = 'commission_currency_id';

    /** How long a percentage commission runs: 0 for the first payment only, months, or null for life. */
    public const string COMMISSION_PERIOD_MONTHS = 'commission_period_months';

    /** Days a cash commission waits before it is payable, 0 to the options' maximum. */
    public const string HOLD_DAYS = 'hold_days';

    /** A `ReferralFriendRewardKind` value. */
    public const string FRIEND_REWARD_KIND = 'friend_reward_kind';

    /** Days banked on the friend's first purchase, when that is the reward. */
    public const string FRIEND_REWARD_DAYS = 'friend_reward_days';

    /** One of the project's coupon codes, by id, when that is the friend's reward. */
    public const string FRIEND_COUPON_ID = 'friend_coupon_id';

    /** Whether only members with a live membership may join. */
    public const string CUSTOMERS_ONLY = 'customers_only';

    /** Whether the creator approves each affiliate before their code counts. */
    public const string APPROVAL_REQUIRED = 'approval_required';

    /** The question affiliates answer so the creator can pay them, or null for none. */
    public const string PAYOUT_DETAILS_LABEL = 'payout_details_label';

    /** The creator's own terms, or null for none. */
    public const string TERMS = 'terms';

    /** The plans conversions are restricted to, as a list of ids; an empty list means every plan. */
    public const string PLAN_IDS = 'plan_ids';

    /**
     * @param  array<string, mixed>  $fields  The fields the draft names, keyed by the constants above.
     */
    private function __construct(
        public array $fields,
    ) {}

    /**
     * @return  self  A draft that names nothing yet.
     */
    public static function empty(): self
    {
        return new self([]);
    }

    /**
     * @param   string  $field  One of the field constants.
     * @param   mixed   $value  Its value; null clears a clearable field.
     * @return  self    A draft naming that field too.
     */
    public function with(string $field, mixed $value): self
    {
        return new self([...$this->fields, $field => $value]);
    }

    /**
     * @param   string  $field  One of the field constants.
     * @return  bool    True when the draft names the field, with any value.
     */
    public function names(string $field): bool
    {
        return array_key_exists($field, $this->fields);
    }
}
