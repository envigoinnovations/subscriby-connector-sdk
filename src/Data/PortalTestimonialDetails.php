<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One member's quote on the portal, as a creator reads it on a connector.
 */
final readonly class PortalTestimonialDetails
{
    /**
     * @param  string       $quote      What they said.
     * @param  string       $name       Who said it.
     * @param  string|null  $subtitle   What they are, a title or a city, or null for nothing under the name.
     * @param  string|null  $avatarUrl  Their photo's address, or null for their initial.
     */
    public function __construct(
        public string $quote,
        public string $name,
        public ?string $subtitle,
        public ?string $avatarUrl,
    ) {}
}
