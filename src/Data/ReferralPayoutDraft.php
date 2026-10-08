<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * Money a creator paid an affiliate, as their surface records it.
 *
 * The creator moves the money through their own gateway or wallet; the
 * core only records that it happened, in which currency and against which
 * commissions. The amount may not exceed what is payable in that currency,
 * and the core refuses one that does.
 */
final readonly class ReferralPayoutDraft
{
    /**
     * @param  string       $amount      What was paid, as a decimal string.
     * @param  string       $currencyId  The currency it was paid in, by id, from the affiliate's balance lines.
     * @param  string|null  $reference   The creator's own reference for the transfer, or null.
     * @param  string|null  $note        Anything they want to remember about it, or null.
     */
    public function __construct(
        public string $amount,
        public string $currencyId,
        public ?string $reference = null,
        public ?string $note = null,
    ) {}
}
