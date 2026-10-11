<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What the core answers when a member arrives through a Smart Link's deep link.
 *
 * The arrival itself is silent on a bot: the member sees the same screen as
 * everyone else. The one thing a connector needs back is the plan the link
 * sells, when it sells one, so the bot can open that plan's checkout instead
 * of the home screen.
 */
final readonly class SmartLinkArrival
{
    /**
     * @param  string       $linkName  The link's name, for a connector's own log line.
     * @param  string|null  $planId    The plan the link's destination names, when the project still offers it; null for a portal or bot destination.
     */
    public function __construct(
        public string $linkName,
        public ?string $planId,
    ) {}
}
