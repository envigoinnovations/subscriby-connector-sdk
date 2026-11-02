<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * Where a pass stands today, in the core's own words.
 */
final readonly class PassStanding
{
    /**
     * @param  string       $state        The pass access state: `pending`, `granted`, `revoked` or `missed`.
     * @param  string       $headline     The one line the portal shows about the window that governs the pass now.
     * @param  string|null  $windowRange  The window's dates as the portal prints them, or null while unconfirmed.
     * @param  string|null  $joinNotice   What the member must still do to get in, or null when nothing.
     */
    public function __construct(
        public string $state,
        public string $headline,
        public ?string $windowRange = null,
        public ?string $joinNotice = null,
    ) {}
}
