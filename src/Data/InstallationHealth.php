<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\DeliveryFailureKind;
use Subscriby\Connector\Enums\InstallationState;

/**
 * What a probe found when it asked the platform about an installation.
 *
 * `reason` is a stable machine word the connector chooses (`token_revoked`,
 * `intents_disallowed`), stored on the row and carried in webhook payloads;
 * `detail` is the sentence a creator reads. `creatorActionable` says whether
 * the creator can fix it themselves, which decides whether the dashboard shows
 * an instruction or an incident. `failureKind` is the same refusal by the
 * core's own kinds, so a caller can tell a revoked credential from a network
 * blip without reading the connector's word.
 */
final readonly class InstallationHealth
{
    /**
     * @param  InstallationState         $state              The state the probe concluded.
     * @param  string|null               $reason             A stable machine word for why, or null when healthy.
     * @param  string                    $detail             What a creator reads.
     * @param  bool                      $creatorActionable  Whether the creator can act on it.
     * @param  DeliveryFailureKind|null  $failureKind        Why the probe failed, by kind, or null when healthy.
     */
    public function __construct(
        public InstallationState $state,
        public ?string $reason = null,
        public string $detail = '',
        public bool $creatorActionable = false,
        public ?DeliveryFailureKind $failureKind = null,
    ) {}

    /**
     * @return  self  A probe that found nothing wrong.
     */
    public static function healthy(): self
    {
        return new self(InstallationState::Connected);
    }

    /**
     * @return  bool  True when the installation can still act.
     */
    public function isHealthy(): bool
    {
        return $this->state === InstallationState::Connected;
    }
}
