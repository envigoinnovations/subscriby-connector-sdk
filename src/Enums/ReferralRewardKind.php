<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * What a project's Referral Program pays a referrer, as a connector's creator surface sets it.
 *
 * Free days are banked on the referrer's membership by the core; cash is a
 * commission the creator pays out themselves and records against the
 * affiliate's balance.
 */
enum ReferralRewardKind: string
{
    use EnumHelpers;

    case FreeDays = 'free_days';
    case Cash = 'cash';

    /**
     * @return  bool  True when referrers earn money rather than membership days.
     */
    public function paysCash(): bool
    {
        return $this === self::Cash;
    }
}
