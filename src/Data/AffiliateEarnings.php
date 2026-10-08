<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a creator owes an affiliate in one currency, formatted for a screen.
 *
 * Three figures a member asks about: what is still inside its hold, what is
 * payable now, and what the creator already paid them. Formatted by the core
 * in the currency's own style, so a connector prints them as they are.
 */
final readonly class AffiliateEarnings
{
    /**
     * @param  string  $currency  The currency's ISO code.
     * @param  string  $pending   Money still inside its hold.
     * @param  string  $payable   Money past its hold and not yet paid out.
     * @param  string  $paidOut   Money the creator recorded as paid.
     */
    public function __construct(
        public string $currency,
        public string $pending,
        public string $payable,
        public string $paidOut,
    ) {}
}
