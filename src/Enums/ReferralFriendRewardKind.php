<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * What a referred friend receives on their first purchase, as the creator set it.
 *
 * A coupon is one of the project's own codes, applied at the friend's
 * checkout; free days are banked on their first membership by the core;
 * `None` leaves the referrer's reward as the programme's only one.
 */
enum ReferralFriendRewardKind: string
{
    use EnumHelpers;

    case None = 'none';
    case FreeDays = 'free_days';
    case Coupon = 'coupon';

    /**
     * @return  bool  True when the friend is promised something on their first purchase.
     */
    public function rewardsFriend(): bool
    {
        return $this !== self::None;
    }
}
