<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One address a partner shares, labelled by where it lands.
 *
 * The web link is the one every partner has; a connector that registers
 * creators adds a deep link of its own, labelled with its name, so a
 * partner on a bot can hand out the address that opens the same bot.
 */
final readonly class PartnerLink
{
    /**
     * @param  string  $label  Where the link lands, for the creator ("Web", a connector's name).
     * @param  string  $url    The address, carrying the partner's code.
     */
    public function __construct(
        public string $label,
        public string $url,
    ) {}
}
