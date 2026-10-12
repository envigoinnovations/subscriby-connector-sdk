<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a creator changed on the Revenue Recovery settings, one field or several.
 *
 * Null means "leave it as it is", so a bot that flips one switch sends one
 * field; a coupon is cleared by sending the empty string, since null cannot
 * mean both.
 */
final readonly class RetentionSettingsDraft
{
    /** The value that clears a coupon choice. */
    public const string NONE = '';

    /**
     * @param  int|null     $graceDays           The grace window in days, or null to keep it.
     * @param  bool|null    $digestEnabled       Whether the owner gets the digest, or null to keep it.
     * @param  bool|null    $feedbackEnabled     Whether a cancelling member is asked why, or null to keep it.
     * @param  bool|null    $pauseOffered        Whether a pause is offered, or null to keep it.
     * @param  string|null  $discountCouponId    The coupon to offer as a discount, `NONE` to offer none, or null to keep it.
     * @param  string|null  $returnCouponId      The coupon to hand over, `NONE` to hand none, or null to keep it.
     * @param  int|null     $checkoutStallHours  How many hours a checkout may stay open before it counts as stalled, or null to keep it.
     * @param  int|null     $trialNoticeDays     How many days before a trial without a card ends its sequence begins, or null to keep it.
     */
    public function __construct(
        public ?int $graceDays = null,
        public ?bool $digestEnabled = null,
        public ?bool $feedbackEnabled = null,
        public ?bool $pauseOffered = null,
        public ?string $discountCouponId = null,
        public ?string $returnCouponId = null,
        public ?int $checkoutStallHours = null,
        public ?int $trialNoticeDays = null,
    ) {}
}
