<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A place on a connector that a resource points at: a chat, a guild, a role, a channel.
 *
 * `kind` is the connector's own vocabulary (`channel`, `supergroup`, `role`);
 * `parentExternalId` holds the container when the platform nests places, a
 * Discord role inside its guild.
 */
final readonly class SpaceRef
{
    /**
     * @param  string       $id                The space row's UUID.
     * @param  string       $connector         The connector key.
     * @param  string       $externalId        The platform's id for the place.
     * @param  string       $kind              The connector's kind for it, as declared in the manifest.
     * @param  string|null  $storageRef        The connector's own row id for it, opaque to the core.
     * @param  string|null  $parentExternalId  The containing place on platforms that nest them.
     */
    public function __construct(
        public string $id,
        public string $connector,
        public string $externalId,
        public string $kind,
        public ?string $storageRef = null,
        public ?string $parentExternalId = null,
    ) {}
}
