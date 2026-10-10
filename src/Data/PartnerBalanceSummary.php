<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What an approved partner has earned and where it stands, every amount already formatted in the programme's currency.
 *
 * The core formats the figures ("$12.50") so a connector never composes
 * money from a decimal and a code; the currency is named beside them for
 * a connector that wants to say it.
 */
final readonly class PartnerBalanceSummary
{
    /**
     * @param  string  $earned    Everything earned that was not reversed: pending, payable, credited and paid together.
     * @param  string  $pending   Commissions still waiting out their hold.
     * @param  string  $payable   Approved and not yet credited or paid out.
     * @param  string  $credited  Applied to the partner's own invoices as credit.
     * @param  string  $paid      Paid out in cash.
     * @param  string  $currency  The ISO code the amounts are in.
     */
    public function __construct(
        public string $earned,
        public string $pending,
        public string $payable,
        public string $credited,
        public string $paid,
        public string $currency,
    ) {}
}
