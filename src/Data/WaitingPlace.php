<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * A place a purchase waits for, because the member holds no account on its connector.
 *
 * A member who bought through one platform a plan that includes a place on
 * another has access the core cannot give until they connect an account
 * there. A surface that lists what the member holds lists these beside the
 * doors still open, in the member's words for the place, with the
 * connector's name and the signed portal door that signs the member in and
 * opens the connect flow, so the member is one tap from the account the
 * place is waiting for.
 */
final readonly class WaitingPlace
{
    /**
     * @param  ResourceRef             $resource       The place.
     * @param  string                  $connector      The connector the place is on, which the member holds no account on.
     * @param  string                  $connectorName  The connector's name as the member reads it.
     * @param  string                  $kindBadge      The member's words for the place: the connector-named kind, then the home where the place has one.
     * @param  string|null             $connectDoor    The signed portal address that signs the member in and opens the connect flow; null while the project has no portal handle.
     * @param  string|null             $windowId       The dated window the place waits for, or null for undated access.
     * @param  DateTimeImmutable|null  $opensAt        When that window opens, or null.
     */
    public function __construct(
        public ResourceRef $resource,
        public string $connector,
        public string $connectorName,
        public string $kindBadge,
        public ?string $connectDoor = null,
        public ?string $windowId = null,
        public ?DateTimeImmutable $opensAt = null,
    ) {}
}
