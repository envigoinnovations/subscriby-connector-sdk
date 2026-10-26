<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One part of the portal in its place, as a creator reads it on a connector.
 */
final readonly class PortalPlacementDetails
{
    /**
     * @param  string  $key    The part, a stable key the draft sends back.
     * @param  string  $label  The part's name in the creator's language.
     * @param  bool    $shown  Whether the portal draws it.
     */
    public function __construct(
        public string $key,
        public string $label,
        public bool $shown,
    ) {}
}
