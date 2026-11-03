<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A coupon as a creator's surface asks the core to create it.
 *
 * A percentage takes no currency; a fixed amount takes the currency of the
 * plans it applies to. The code is stored upper-cased, and a name left out
 * is the code itself. Dates are ISO 8601 with a zone.
 */
final readonly class CouponDraft
{
    /** A discount of a percentage of the price. */
    public const string PERCENTAGE = 'percentage';

    /** A discount of a fixed amount in a currency. */
    public const string FIXED = 'fixed';

    /**
     * @param  string        $code            The code members type, letters, digits and dashes, 3 to 64 characters.
     * @param  string        $discountType    `percentage` or `fixed`.
     * @param  string        $discountValue   The percentage (1 to 100) or the amount, as a decimal string.
     * @param  string|null   $currencyId      The currency a fixed amount is in, by id; null for a percentage.
     * @param  string|null   $name            What the creator calls it, or null for the code.
     * @param  int|null      $maxRedemptions  How many members may redeem it in all, or null for no limit.
     * @param  list<string>  $planIds         The plans it applies to, by id; empty for every plan.
     * @param  string|null   $startsAt        When it starts working, or null for now.
     * @param  string|null   $expiresAt       When it stops working, or null for never.
     */
    public function __construct(
        public string $code,
        public string $discountType,
        public string $discountValue,
        public ?string $currencyId = null,
        public ?string $name = null,
        public ?int $maxRedemptions = null,
        public array $planIds = [],
        public ?string $startsAt = null,
        public ?string $expiresAt = null,
    ) {}
}
