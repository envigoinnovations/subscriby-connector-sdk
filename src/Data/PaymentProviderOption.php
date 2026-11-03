<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A gateway the core offers a project, with what connecting it takes.
 *
 * A gateway either onboards (the creator is sent to the gateway's own
 * pages and comes back connected) or is configured from the keys the
 * gateway issued, which the fields list; a connector shows one door or the
 * other and never names a gateway of its own.
 */
final readonly class PaymentProviderOption
{
    /**
     * @param  PaymentProviderKey        $key       The gateway, as a method names it.
     * @param  string                    $label     The gateway's name as the creator reads it.
     * @param  list<PaymentFieldOption>  $fields    The values a configured method needs; empty for a gateway that onboards.
     * @param  bool                      $onboards  True when connecting runs through the gateway's own onboarding rather than typed keys.
     * @param  bool                      $sandbox   True when the gateway also runs in a sandbox mode.
     */
    public function __construct(
        public PaymentProviderKey $key,
        public string $label,
        public array $fields = [],
        public bool $onboards = false,
        public bool $sandbox = true,
    ) {}
}
