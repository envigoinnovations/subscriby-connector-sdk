<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * How a partner asks to be paid, as a connector's conversation collected it.
 *
 * The rail decides what else is needed: one detail for a rail that pays to
 * an email or a wallet, a bank account for one that transfers, nothing for
 * credit. A cash rail also needs the legal name, the country of tax
 * residence and the US-person declaration, and the first save needs the
 * terms accepted; the core refuses what is missing with a reason.
 */
final readonly class PartnerPayoutDraft
{
    /**
     * @param  string                 $method       The rail's value ({@see PartnerRailOption::$value}).
     * @param  string|null            $detail       The one detail a single-detail rail asks for; null otherwise.
     * @param  string|null            $legalName    The partner's legal name; null to keep what was stored.
     * @param  string|null            $taxCountry   The country of tax residence, two letters; null to keep what was stored.
     * @param  bool|null              $usPerson     Whether the partner is a US person; null to keep what was stored.
     * @param  bool                   $acceptTerms  Whether the partner accepted the Partner Program Terms in this conversation.
     * @param  PartnerBankDraft|null  $bank         The bank account for a rail that transfers to one; null otherwise.
     */
    public function __construct(
        public string $method,
        public ?string $detail = null,
        public ?string $legalName = null,
        public ?string $taxCountry = null,
        public ?bool $usPerson = null,
        public bool $acceptTerms = false,
        public ?PartnerBankDraft $bank = null,
    ) {}
}
