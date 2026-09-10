<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\DeliveryFailure;

/**
 * Reading why the platform refused a call.
 *
 * Required of every connector. Each platform has its own error vocabulary and
 * its own habit of refusing inside a successful HTTP response; the connector
 * reads both shapes and the core decides once, from the kind, whether to
 * retry, wait, alert the creator or give up.
 */
interface FailureClassifier
{
    /**
     * @param   mixed            $responseOrThrowable  The platform's answer, or the exception the call raised.
     * @return  DeliveryFailure  The classified reason.
     */
    public function classify(mixed $responseOrThrowable): DeliveryFailure;
}
