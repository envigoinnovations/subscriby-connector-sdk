<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A place the core mirrors, with the standby its posts are copied into.
 *
 * The core reads no connector's posts: the connector hears a post on its
 * platform, asks which of its places are mirrored, and copies the post into
 * the standby through its own recovery port.
 */
final readonly class MirrorPair
{
    /**
     * @param  ResourceRef  $resource  The resource sold in the mirrored place.
     * @param  SpaceRef     $source    The place whose posts are copied.
     * @param  SpaceRef     $standby   The place the copies land in.
     */
    public function __construct(
        public ResourceRef $resource,
        public SpaceRef $source,
        public SpaceRef $standby,
    ) {}
}
