<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * The one thing a project may offer a member instead of the cancellation.
 *
 * A pause holds the charge for one billing cycle and keeps the membership;
 * a discount lowers the renewals and keeps it; a return coupon is a code
 * handed over on the way out, so the cancellation goes on. A connector
 * reads which one it was shown and whether the membership stays.
 */
enum RetentionOfferKind: string
{
    use EnumHelpers;

    case Pause = 'pause';
    case Discount = 'discount';
    case ReturnCoupon = 'return_coupon';

    /**
     * @return  bool  True when taking the offer keeps the membership, so no cancellation follows.
     */
    public function keepsSubscription(): bool
    {
        return $this !== self::ReturnCoupon;
    }
}
