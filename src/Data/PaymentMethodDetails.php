<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\PaymentMode;

/**
 * A project's payment method as a creator surface lists it: the gateway, the mode, and whether it can take money now.
 */
final readonly class PaymentMethodDetails
{
    /**
     * @param  string                  $id              The method's id.
     * @param  string                  $projectId       The project.
     * @param  PaymentProviderKey      $provider        The gateway.
     * @param  string                  $label           The gateway's name as the creator reads it.
     * @param  PaymentMode             $mode            Real money or the sandbox.
     * @param  bool                    $active          Whether checkouts offer it.
     * @param  bool                    $linked          Whether the gateway's account is attached; false for an onboarding never finished.
     * @param  bool                    $chargesEnabled  Whether the gateway reports it can charge; always true for a gateway configured from keys.
     * @param  DateTimeImmutable|null  $createdAt       When it was configured.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public PaymentProviderKey $provider,
        public string $label,
        public PaymentMode $mode,
        public bool $active,
        public bool $linked,
        public bool $chargesEnabled,
        public ?DateTimeImmutable $createdAt = null,
    ) {}

    /**
     * @return  bool  True when the method is on and the gateway can charge, so a plan sold through it takes money.
     */
    public function takesMoney(): bool
    {
        return $this->active && $this->linked && $this->chargesEnabled;
    }
}
