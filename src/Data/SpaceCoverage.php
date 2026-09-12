<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One gated resource's recovery standing: whether a standby waits behind it and in what state.
 */
final readonly class SpaceCoverage
{
    /**
     * @param  string  $resourceId      The application's resource id.
     * @param  string  $kind            The resource kind within the connector (`channel`, `role`, …).
     * @param  bool    $hasStandby      Whether a standby space is linked.
     * @param  bool    $standbyHealthy  Whether the last probe found the standby usable; false when there is none.
     * @param  bool    $mirrored        Whether posts are copied into the standby as they are made.
     */
    public function __construct(
        public string $resourceId,
        public string $kind,
        public bool $hasStandby,
        public bool $standbyHealthy,
        public bool $mirrored,
    ) {}
}
