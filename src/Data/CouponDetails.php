<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * A coupon as its creator manages it, in the core's words.
 */
final readonly class CouponDetails
{
    /**
     * @param  string                  $id              The coupon's UUID.
     * @param  string                  $projectId       The project's UUID.
     * @param  string                  $code            The code members type, upper-cased.
     * @param  string                  $name            What the creator calls it.
     * @param  string                  $discountType    `percentage` or `fixed`.
     * @param  string                  $discountValue   The percentage or the amount, as a decimal string.
     * @param  string|null             $currency        The ISO code a fixed amount is in, null for a percentage.
     * @param  bool                    $active          Whether members may redeem it now.
     * @param  int|null                $maxRedemptions  How many members may redeem it in all, or null for no limit.
     * @param  int                     $redemptions     How many have.
     * @param  DateTimeImmutable|null  $startsAt        When it starts working, or null for already.
     * @param  DateTimeImmutable|null  $expiresAt       When it stops working, or null for never.
     * @param  list<string>            $planIds         The plans it applies to, by id; empty for every plan.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public string $code,
        public string $name,
        public string $discountType,
        public string $discountValue,
        public ?string $currency,
        public bool $active,
        public ?int $maxRedemptions,
        public int $redemptions,
        public ?DateTimeImmutable $startsAt,
        public ?DateTimeImmutable $expiresAt,
        public array $planIds,
    ) {}
}
