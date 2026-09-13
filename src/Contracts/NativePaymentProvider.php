<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts;

use Subscriby\Connector\Data\Field;

/**
 * A payment method that exists only because of a connector, such as Telegram Stars.
 *
 * Declared through the `ProvidesPaymentMethods` port, which the registry
 * refuses to bind for anything but an official connector because it sits on
 * the money path. This contract carries what the payment-methods screens need
 * to offer the provider; the checkout, settlement and refund methods join it
 * in the payments slice of the program, where the settlement service that
 * receives them is generalised.
 */
interface NativePaymentProvider
{
    /**
     * @return  string  The provider key in `connector:provider` form, such as `telegram:stars`.
     */
    public function key(): string;

    /**
     * @return  string  The name the picker shows.
     */
    public function label(): string;

    /**
     * @return  list<string>  The currency codes the provider settles in.
     */
    public function currencies(): array;

    /**
     * @return  list<Field>  What the creator fills in to enable it, empty when nothing is needed.
     */
    public function setupFields(): array;

    /**
     * @return  bool  True when a test-mode method can exist alongside the live one.
     */
    public function supportsSandbox(): bool;

    /**
     * @return  string  The file name, without extension, of the provider's mark under the platform's payment icons.
     */
    public function icon(): string;

    /**
     * @return  string  One line under the provider's name in the picker, saying where it works and what a unit is worth.
     */
    public function tagline(): string;

    /**
     * The fixed value of one unit of the provider's currency in US dollars, when the platform fixes it.
     *
     * A native currency no exchange feed carries needs its rate from the
     * connector: the rate job records the reciprocal as units per dollar,
     * the form every other currency's rate takes. Null when the currency
     * floats and a feed carries it.
     *
     * @return  float|null  Dollars per unit, or null.
     */
    public function unitInUsd(): ?float;
}
