<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One entry of the portal's social-link row as a creator reads it on a connector.
 */
final readonly class PortalSocialLinkDetails
{
    /**
     * @param  string       $network       The network's value, as a draft names it.
     * @param  string       $networkLabel  The network's name in the creator's language.
     * @param  string       $url           The address.
     * @param  string|null  $label         The creator's own name for a custom link, or null for a named network.
     * @param  string       $title         The word the portal shows beside the icon: the label for a custom link, else the network's name.
     */
    public function __construct(
        public string $network,
        public string $networkLabel,
        public string $url,
        public ?string $label,
        public string $title,
    ) {}
}
