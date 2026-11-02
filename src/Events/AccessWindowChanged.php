<?php

declare(strict_types=1);

namespace Subscriby\Connector\Events;

use DateTimeImmutable;
use Subscriby\Connector\Data\ResourceRef;
use Subscriby\Connector\Enums\AccessWindowState;

/**
 * The core has scheduled, moved, opened, closed or cancelled a dated window of access on a resource.
 *
 * Dispatched by the core for the windows a pass sells on a gated place, so a
 * connector can mirror them on its platform (a scheduled event in a server
 * that members mark themselves interested in) and keep the mirror in step
 * without listening to the core's own pass events. The grants themselves
 * still arrive through the `AccessController` at opening time; this event
 * is about the window, not the holders.
 */
final readonly class AccessWindowChanged
{
    /**
     * @param  ResourceRef             $resource   The place the window is on.
     * @param  string                  $windowId   The core's id for the window, stable across changes.
     * @param  string                  $title      What the window is called, as the creator named it.
     * @param  AccessWindowState       $state      Where the window stands now.
     * @param  DateTimeImmutable|null  $opensAt    When access begins, null when the window has no fixed start.
     * @param  DateTimeImmutable|null  $closesAt   When access ends, null when it has no fixed end.
     * @param  string|null             $portalUrl  Where a member buys or reads about the window, for a mirror that links back.
     */
    public function __construct(
        public ResourceRef $resource,
        public string $windowId,
        public string $title,
        public AccessWindowState $state,
        public ?DateTimeImmutable $opensAt,
        public ?DateTimeImmutable $closesAt,
        public ?string $portalUrl = null,
    ) {}
}
