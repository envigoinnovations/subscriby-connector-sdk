<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * Everything the core needs to write, or rewrite, one space row.
 *
 * Handed to the Core API's `Spaces::record()`, which is idempotent on the
 * installation, the kind and the platform's own id for the place. Binding the
 * space to a resource is a separate call, because a place is known before a
 * creator decides to sell it.
 */
final readonly class SpaceRecord
{
    /**
     * @param  string                $connector         The connector key.
     * @param  string                $installationId    The installation that administers the place.
     * @param  string                $externalId        The platform's id for the place.
     * @param  string                $kind              The connector's kind for it, one the manifest declares.
     * @param  string|null           $title             The name the platform reports.
     * @param  string|null           $parentExternalId  The containing place on platforms that nest them.
     * @param  string|null           $storageRef        The connector's own row id for it, opaque to the core.
     * @param  array<string, mixed>  $meta              Anything else the connector wants kept with the space.
     */
    public function __construct(
        public string $connector,
        public string $installationId,
        public string $externalId,
        public string $kind,
        public ?string $title = null,
        public ?string $parentExternalId = null,
        public ?string $storageRef = null,
        public array $meta = [],
    ) {}
}
