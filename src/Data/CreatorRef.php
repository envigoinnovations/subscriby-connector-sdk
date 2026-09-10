<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A creator (a dashboard user), as a connector may refer to them.
 */
final readonly class CreatorRef
{
    /**
     * @param  string       $id      The user's UUID.
     * @param  string|null  $locale  The locale the creator reads in, for copy the connector renders to them.
     */
    public function __construct(
        public string $id,
        public ?string $locale = null,
    ) {}
}
