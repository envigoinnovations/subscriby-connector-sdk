<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A gateway's hosted checkout the core started for a member, with the address to send them to.
 */
final readonly class HostedCheckout
{
    /**
     * @param  string              $url            The gateway's hosted page the member pays on.
     * @param  PaymentProviderKey  $provider       The rail the checkout runs on.
     * @param  string              $providerLabel  The rail's name as the member reads it, translated.
     */
    public function __construct(
        public string $url,
        public PaymentProviderKey $provider,
        public string $providerLabel,
    ) {}
}
