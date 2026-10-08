<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One address an affiliate hands out, labelled by where it lands.
 *
 * A connector's deep link is labelled with the connector's name and the
 * portal link with the core's word for the web, so a bot lists them as the
 * dashboard does.
 */
final readonly class ReferralLink
{
    /**
     * @param  string  $label  Where the link lands, in the member's language.
     * @param  string  $url    The address, the affiliate's code inside it.
     */
    public function __construct(
        public string $label,
        public string $url,
    ) {}
}
