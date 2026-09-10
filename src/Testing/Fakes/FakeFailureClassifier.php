<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\FailureClassifier;
use Subscriby\Connector\Data\DeliveryFailure;
use Subscriby\Connector\Enums\DeliveryFailureKind;
use Throwable;

/**
 * Classifies the fake platform's refusals.
 *
 * Exceptions are transient; an array carrying `error` maps a few named codes
 * to kinds so tests can hand back any class of failure.
 */
final class FakeFailureClassifier implements FailureClassifier
{
    /**
     * @param   mixed            $responseOrThrowable  The platform's answer or the exception.
     * @return  DeliveryFailure  The classified reason.
     */
    public function classify(mixed $responseOrThrowable): DeliveryFailure
    {
        if ($responseOrThrowable instanceof Throwable) {
            return new DeliveryFailure(DeliveryFailureKind::Transient, $responseOrThrowable->getMessage());
        }

        $error = is_array($responseOrThrowable) ? (string) ($responseOrThrowable['error'] ?? '') : '';

        $kind = match ($error) {
            'blocked' => DeliveryFailureKind::Unreachable,
            'forbidden' => DeliveryFailureKind::NotPermitted,
            'missing' => DeliveryFailureKind::TargetMissing,
            'slow_down' => DeliveryFailureKind::RateLimited,
            'misconfigured' => DeliveryFailureKind::Configuration,
            default => DeliveryFailureKind::Other,
        };

        return new DeliveryFailure($kind, $error, $kind === DeliveryFailureKind::RateLimited ? 5 : null);
    }
}
