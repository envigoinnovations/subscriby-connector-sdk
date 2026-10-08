<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One thing a creator may pick while managing a Referral Program, by id and by the words a screen shows.
 *
 * The same shape serves a coupon code for the friend reward, a plan the
 * programme may be restricted to, a currency a fixed commission is paid in,
 * and a member found by a search, so a connector renders one kind of
 * picker and hands the id straight back to the core.
 */
final readonly class ReferralOption
{
    /**
     * @param  string  $id     The row's UUID, as the core will take it back.
     * @param  string  $label  What a creator reads on the button: a coupon code, a plan name, a currency code, a member's name.
     */
    public function __construct(
        public string $id,
        public string $label,
    ) {}
}
