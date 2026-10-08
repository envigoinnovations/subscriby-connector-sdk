<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a creator may choose from, and the limits the core holds, when setting a project's Referral Program up.
 *
 * Read once before a settings conversation starts: whether the project
 * owner's plan includes the programme at all (so a connector refuses with
 * the upgrade path instead of walking a creator through settings it will
 * not save), whether it includes coupon codes (so the friend-coupon choice
 * is offered or not), the pickable coupons, plans and currencies, and the
 * figures the core validates against, so a connector's prompts quote the
 * same limits the dashboard does.
 */
final readonly class ReferralProgramOptions
{
    /**
     * @param  bool                  $referralsAvailable   Whether the project owner's plan or an addon includes the Referral Program.
     * @param  bool                  $couponsAvailable     Whether the project owner's plan or an addon includes coupon codes, which the friend-coupon reward needs.
     * @param  list<ReferralOption>  $coupons              The project's coupon codes a friend may be rewarded with, by code; empty without coupon codes on the plan.
     * @param  list<ReferralOption>  $plans                The project's plans the programme may be restricted to, by name.
     * @param  list<ReferralOption>  $currencies           The currencies a fixed commission may be paid in, by ISO code.
     * @param  int                   $windowDays           How many days a referred friend stays attributed after arriving.
     * @param  int                   $defaultHoldDays      The hold a new programme starts with.
     * @param  int                   $maxHoldDays          The longest hold the core accepts.
     * @param  int                   $maxCommissionMonths  The longest commission period the core accepts; longer means for life.
     * @param  int                   $maxRewardDays        The most membership days one conversion may grant.
     */
    public function __construct(
        public bool $referralsAvailable,
        public bool $couponsAvailable,
        public array $coupons,
        public array $plans,
        public array $currencies,
        public int $windowDays,
        public int $defaultHoldDays,
        public int $maxHoldDays,
        public int $maxCommissionMonths,
        public int $maxRewardDays,
    ) {}
}
