<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\DeliveryFailureKind;

/**
 * Why a platform call did not do what was asked, classified.
 */
final readonly class DeliveryFailure
{
    /**
     * @param  DeliveryFailureKind  $kind               The class of failure the core acts on.
     * @param  string               $detail             The platform's own words, for the log and the creator.
     * @param  int|null             $retryAfterSeconds  How long the platform asked us to wait, for a rate limit.
     */
    public function __construct(
        public DeliveryFailureKind $kind,
        public string $detail = '',
        public ?int $retryAfterSeconds = null,
    ) {}
}
