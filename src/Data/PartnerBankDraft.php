<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A bank account a partner is paid into, described field by field for a rail that transfers to one.
 *
 * The core trims every field, uppercases the country and the codes and
 * strips the spaces from the account number, and refuses an account missing
 * what a transfer cannot do without (the holder, the bank, the country and
 * the number); the three optional fields help a transfer land.
 */
final readonly class PartnerBankDraft
{
    /**
     * @param  string       $holder         The name on the account.
     * @param  string       $bankName       The bank.
     * @param  string       $country        The bank's country, two letters.
     * @param  string       $accountNumber  The account number or IBAN.
     * @param  string|null  $swift          The SWIFT/BIC, 8 or 11 characters, or null.
     * @param  string|null  $branchCode     A branch, routing, sort or IFSC code, or null.
     * @param  string|null  $address        The holder's address, or null.
     */
    public function __construct(
        public string $holder,
        public string $bankName,
        public string $country,
        public string $accountNumber,
        public ?string $swift = null,
        public ?string $branchCode = null,
        public ?string $address = null,
    ) {}
}
