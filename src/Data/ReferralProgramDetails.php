<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\ReferralCommissionType;
use Subscriby\Connector\Enums\ReferralFriendRewardKind;
use Subscriby\Connector\Enums\ReferralRewardKind;

/**
 * A project's Referral Program as its creator reads it: every setting, its state, its words and its figures.
 *
 * The settings travel typed, so a connector's settings screen can show the
 * current value beside each edit button and pre-fill a prompt; the words
 * (what a referrer earns, how long a commission runs, what a friend gets,
 * the terms as an affiliate hears them) are the core's, so the creator reads
 * the programme exactly as their members do; the figures are the overview's.
 */
final readonly class ReferralProgramDetails
{
    /**
     * @param  string                       $id                      The programme row's UUID.
     * @param  string                       $projectId               The project that runs it.
     * @param  string                       $projectName             The project's name.
     * @param  bool                         $active                  The creator's switch.
     * @param  bool                         $live                    Switched on and still paid for: links attribute and payments earn.
     * @param  ReferralRewardKind           $rewardKind              Free days or cash.
     * @param  int|null                     $rewardDays              Days per converted friend on a free-days programme.
     * @param  ReferralCommissionType|null  $commissionType          How a cash commission is sized; null on a free-days programme.
     * @param  string|null                  $commissionValue         The rate or amount as a decimal string with four places; null on a free-days programme.
     * @param  string|null                  $commissionCurrencyId    The currency a fixed commission is paid in.
     * @param  string|null                  $commissionCurrency      That currency's ISO code.
     * @param  int|null                     $commissionPeriodMonths  How long a percentage commission runs: 0 for the first payment only, 1 upward for months, null for life.
     * @param  int                          $holdDays                Days a cash commission waits before it is payable.
     * @param  ReferralFriendRewardKind     $friendRewardKind        What a referred friend receives.
     * @param  int|null                     $friendRewardDays        Days banked on the friend's first purchase, when that is the reward.
     * @param  string|null                  $friendCouponId          The coupon applied at the friend's checkout, when that is the reward.
     * @param  string|null                  $friendCouponCode        That coupon's code.
     * @param  bool                         $customersOnly           Whether only members with a live membership may join.
     * @param  bool                         $approvalRequired        Whether the creator approves each affiliate before their code counts.
     * @param  string|null                  $payoutDetailsLabel      The question affiliates answer so the creator can pay them, or null.
     * @param  string|null                  $terms                   The creator's own terms, or null.
     * @param  list<string>                 $planIds                 The plans conversions are restricted to; empty for every plan.
     * @param  string                       $rewardLabel             What a referrer earns, in words.
     * @param  string                       $periodLabel             How long a cash commission runs, in words; empty on a free-days programme.
     * @param  string                       $friendRewardLabel       What a friend receives, in words.
     * @param  list<string>                 $termsLines              The terms as an affiliate is told them, one sentence a line.
     * @param  string|null                  $portalUrl               The portal address affiliates' web links land on, or null while the project has no handle.
     * @param  ReferralProgramStats         $stats                   The overview's figures.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public string $projectName,
        public bool $active,
        public bool $live,
        public ReferralRewardKind $rewardKind,
        public ?int $rewardDays,
        public ?ReferralCommissionType $commissionType,
        public ?string $commissionValue,
        public ?string $commissionCurrencyId,
        public ?string $commissionCurrency,
        public ?int $commissionPeriodMonths,
        public int $holdDays,
        public ReferralFriendRewardKind $friendRewardKind,
        public ?int $friendRewardDays,
        public ?string $friendCouponId,
        public ?string $friendCouponCode,
        public bool $customersOnly,
        public bool $approvalRequired,
        public ?string $payoutDetailsLabel,
        public ?string $terms,
        public array $planIds,
        public string $rewardLabel,
        public string $periodLabel,
        public string $friendRewardLabel,
        public array $termsLines,
        public ?string $portalUrl,
        public ReferralProgramStats $stats,
    ) {}
}
