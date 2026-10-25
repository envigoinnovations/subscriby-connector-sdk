<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One entry of the portal's social-link row as a creator sends it.
 */
final readonly class PortalSocialLinkDraft
{
    /**
     * @param  string       $network  A network's value from `PortalDesignDetails::$networks`.
     * @param  string       $url      The address, `https://` or `http://`.
     * @param  string|null  $label    The creator's name for a custom link; ignored for a named network.
     */
    public function __construct(
        public string $network,
        public string $url,
        public ?string $label = null,
    ) {}
}
