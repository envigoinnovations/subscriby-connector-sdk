<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * Where a project carries its support conversations to the creator on the connector.
 *
 * The mode is one of the words the connector's manifest declares under
 * `relay_modes` (or `none` when the creator relays nowhere); the space is
 * the place the creator linked for a mode that mirrors each thread into a
 * shared space, and null for a mode that pings the creator privately.
 */
final readonly class SupportRelayTarget
{
    /**
     * @param  string         $mode   The relay mode the project chose, in the manifest's words, or `none`.
     * @param  SpaceRef|null  $space  The linked relay space, when the mode uses one and the creator has linked it.
     */
    public function __construct(
        public string $mode,
        public ?SpaceRef $space = null,
    ) {}

    /**
     * @param   string  $externalId  The platform's id for a place.
     * @return  bool    True when that place is the project's linked relay space.
     */
    public function isSpace(string $externalId): bool
    {
        return $this->space !== null && $this->space->externalId === $externalId;
    }
}
