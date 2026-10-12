<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One of a project's live coupons, as a creator picks it for an offer.
 */
final readonly class RetentionCouponOption
{
    /**
     * @param  string  $id        The coupon's UUID.
     * @param  string  $code      The code members type.
     * @param  string  $discount  The discount in the core's words ("25% off").
     */
    public function __construct(
        public string $id,
        public string $code,
        public string $discount,
    ) {}
}
