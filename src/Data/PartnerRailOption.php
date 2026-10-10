<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One way a partner may be paid, with what the rail asks for.
 *
 * A rail asks for one typed detail (a PayPal email, a wallet address), for
 * a bank account described field by field, or for nothing (credit on the
 * partner's own invoices); the credit rail is offered only while the core
 * has it switched on.
 */
final readonly class PartnerRailOption
{
    /**
     * @param  string       $value              The rail's value, the one a {@see PartnerPayoutDraft} names.
     * @param  string       $label              The rail in words.
     * @param  string|null  $detailLabel        The one detail the rail asks for, in words; null when it asks for none or for a bank account.
     * @param  bool         $needsSingleDetail  Whether one typed detail is what it needs.
     * @param  bool         $paysToBankAccount  Whether it needs a bank account described field by field.
     * @param  bool         $available          Whether a partner may choose it today.
     */
    public function __construct(
        public string $value,
        public string $label,
        public ?string $detailLabel,
        public bool $needsSingleDetail,
        public bool $paysToBankAccount,
        public bool $available,
    ) {}
}
