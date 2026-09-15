<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A resource the core sells access to, as a connector may name it.
 *
 * A resource is the creator's decision to sell a place; the place itself is
 * a `SpaceRef`. The ref carries what a connector needs to act on the resource
 * or speak about it (the project, the stored kind, the title, the space it
 * gates) and never the plans or the grants behind it, which are the core's to
 * reason about.
 */
final readonly class ResourceRef
{
    /**
     * @param  string        $id         The resource row's UUID.
     * @param  string        $projectId  The project that sells it.
     * @param  ResourceKind  $kind       The stored kind, `connector:kind`; the manual perk while the resource is bound to no place.
     * @param  string        $title      The creator-facing title.
     * @param  string|null   $spaceId    The space it gates, or null while it is bound to none.
     * @param  bool          $active     Whether plans may still sell it.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public ResourceKind $kind,
        public string $title,
        public ?string $spaceId = null,
        public bool $active = true,
    ) {}
}
