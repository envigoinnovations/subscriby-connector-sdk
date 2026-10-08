<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a creator owes one affiliate in one currency, as the creator's surface reads it.
 *
 * The member's own view ({@see AffiliateEarnings}) carries formatted
 * figures alone; the creator's carries the currency's id and the payable
 * amount raw as well, because a payout is recorded in that currency and
 * capped at that amount, and a connector offering "pay everything payable"
 * has to hand the exact figure back.
 */
final readonly class AffiliateBalanceLine
{
    /**
     * @param  string  $currencyId     The currency row's UUID, as a payout names it.
     * @param  string  $currency       The currency's ISO code.
     * @param  string  $pending        Money still inside its hold, formatted.
     * @param  string  $payable        Money past its hold and not yet paid out, formatted.
     * @param  string  $paidOut        Money already recorded as paid, formatted.
     * @param  string  $payableAmount  The payable figure as a decimal string with four places, for a payout of everything owed.
     */
    public function __construct(
        public string $currencyId,
        public string $currency,
        public string $pending,
        public string $payable,
        public string $paidOut,
        public string $payableAmount,
    ) {}

    /**
     * @return  bool  True when something is payable in this currency right now.
     */
    public function hasPayable(): bool
    {
        return bccomp($this->payableAmount, '0', 4) > 0;
    }
}
