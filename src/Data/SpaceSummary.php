<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What the platform says a place is.
 */
final readonly class SpaceSummary
{
    /**
     * @param  string                $externalId        The platform's id for the place.
     * @param  string                $kind              The connector's kind for it, one the manifest declares.
     * @param  string                $title             The name the platform reports.
     * @param  string|null           $parentExternalId  The containing place on platforms that nest them.
     * @param  array<string, mixed>  $meta              Anything else the connector wants kept with the space.
     */
    public function __construct(
        public string $externalId,
        public string $kind,
        public string $title,
        public ?string $parentExternalId = null,
        public array $meta = [],
    ) {}
}
