<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A project's Revenue Recovery settings, as a creator's connector surface shows and edits them.
 *
 * The grace window, the question and the digest run on every plan; the
 * three offers gate on the owner's plan, which `offersAvailable` answers,
 * so a connector hides the offer switches rather than letting a save be
 * refused. The ladder sentence and the bounds are the core's, so a bot
 * explains the programme the way the dashboard does.
 */
final readonly class RetentionSettingsDetails
{
    /**
     * @param  string                       $projectId        The project.
     * @param  string                       $projectName      Its name, for the screen's heading.
     * @param  int                          $graceDays        How many days a failed renewal keeps access.
     * @param  int                          $maxGraceDays     The most the platform allows.
     * @param  string                       $ladderSentence   The notice ladder for that window, in the creator's words.
     * @param  bool                         $digestEnabled    Whether the owner gets the daily digest.
     * @param  bool                         $feedbackEnabled  Whether a cancelling member is asked why.
     * @param  bool                         $offersAvailable  Whether the owner's plan includes the offers.
     * @param  bool                         $pauseOffered     Whether a pause is offered where the rail can hold the charge.
     * @param  RetentionCouponOption|null   $discountCoupon   The coupon offered as a discount on the renewals, or null.
     * @param  RetentionCouponOption|null   $returnCoupon     The coupon handed over to come back with, or null.
     * @param  list<RetentionCouponOption>  $coupons          The project's live coupons a creator may pick, code order.
     */
    public function __construct(
        public string $projectId,
        public string $projectName,
        public int $graceDays,
        public int $maxGraceDays,
        public string $ladderSentence,
        public bool $digestEnabled,
        public bool $feedbackEnabled,
        public bool $offersAvailable,
        public bool $pauseOffered,
        public ?RetentionCouponOption $discountCoupon,
        public ?RetentionCouponOption $returnCoupon,
        public array $coupons,
    ) {}
}
