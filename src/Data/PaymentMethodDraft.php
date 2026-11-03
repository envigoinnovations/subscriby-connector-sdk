<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\PaymentMode;

/**
 * A payment method as a creator configures it from a connector: the gateway, the mode and the keys the gateway issued.
 *
 * The keys are named as `PaymentProviderOption::$fields` lists them for the
 * gateway; the core checks them the way the dashboard's form does before
 * anything is stored, and stores them encrypted.
 */
final readonly class PaymentMethodDraft
{
    /**
     * @param  PaymentProviderKey     $provider  The gateway.
     * @param  PaymentMode            $mode      Real money or the gateway's sandbox.
     * @param  array<string, string>  $config    The gateway's keys by field name.
     */
    public function __construct(
        public PaymentProviderKey $provider,
        public PaymentMode $mode,
        public array $config,
    ) {}
}
