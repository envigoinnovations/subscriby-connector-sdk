<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A project, as a connector may refer to it.
 *
 * Refs are the only way an application row crosses the SDK boundary: an id and
 * the few facts a connector needs, never the Eloquent model, so a connector
 * survives every core refactor that keeps the id.
 */
final readonly class ProjectRef
{
    /**
     * @param  string  $id  The project's UUID.
     */
    public function __construct(
        public string $id,
    ) {}
}
