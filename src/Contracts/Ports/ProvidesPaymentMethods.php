<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Contracts\NativePaymentProvider;

/**
 * Payment methods that exist only because of this connector.
 *
 * Bound by connectors that declare `native_payments`, which the registry
 * reserves for official connectors: a native provider settles sales and
 * meters the platform's fee, and that path is not open to a package Subscriby
 * has not reviewed.
 */
interface ProvidesPaymentMethods
{
    /**
     * @return  list<NativePaymentProvider>  The providers, each keyed `connector:provider`.
     */
    public function paymentProviders(): array;
}
