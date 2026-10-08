<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * How a cash commission is sized: a share of every settled payment for a period, or one amount on the first.
 *
 * A fixed commission is paid once, on the referred friend's first payment,
 * in the currency the creator chose; a percentage runs on every payment for
 * the programme's commission period.
 */
enum ReferralCommissionType: string
{
    use EnumHelpers;

    case Percentage = 'percentage';
    case Fixed = 'fixed';

    /**
     * @return  bool  True when the commission is one amount on the first payment rather than a share of each.
     */
    public function isFixed(): bool
    {
        return $this === self::Fixed;
    }
}
