<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One member's quote on the portal, as a creator sends it.
 *
 * A connector that resends the list keeps each quote's `avatarUrl` as it
 * read it, because a photo is uploaded on the dashboard alone and a list
 * sent without it would drop the photo.
 */
final readonly class PortalTestimonialDraft
{
    /**
     * @param  string       $quote      What they said, within `PortalDesignDetails::$mediaLimits['testimonial_quote']`.
     * @param  string       $name       Who said it, within `mediaLimits['testimonial_name']`.
     * @param  string|null  $subtitle   What they are, a title or a city, within `mediaLimits['testimonial_subtitle']`, or null.
     * @param  string|null  $avatarUrl  Their photo's address as read from the details, or null.
     */
    public function __construct(
        public string $quote,
        public string $name,
        public ?string $subtitle = null,
        public ?string $avatarUrl = null,
    ) {}
}
