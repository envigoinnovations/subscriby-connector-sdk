<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What the portal shows once a connector sign-in has begun.
 *
 * A bot-based connector returns the deep link that opens the bot with the
 * handshake token and the command to type by hand; an OAuth connector returns
 * the URL to redirect to. The portal polls the handshake until the connector
 * completes it.
 */
final readonly class PortalLoginStart
{
    /**
     * @param  string       $token         The handshake token the account must present.
     * @param  string|null  $url           Where the visitor is sent, or null when the instructions are enough.
     * @param  string|null  $instructions  What to do by hand, for clients where the link cannot open.
     */
    public function __construct(
        public string $token,
        public ?string $url = null,
        public ?string $instructions = null,
    ) {}
}
