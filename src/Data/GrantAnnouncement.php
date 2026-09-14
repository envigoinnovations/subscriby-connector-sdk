<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The core asking a connector to tell a holder what they now hold.
 *
 * Raised once a purchase, a renewal or a reissue has issued its grants: the
 * connector phrases access in its own idiom (a list of links to tap, a role
 * that appeared, a seat that opened) and knows how a holder sees the rest of
 * what they hold, so the core names only what this run issued and, for a
 * dated pass, the window it was about.
 */
final readonly class GrantAnnouncement
{
    /**
     * @param  list<GrantRef>  $grants    The grants this run issued, in the order they were issued.
     * @param  string|null     $windowId  The access window the announcement is about, or null for undated access.
     */
    public function __construct(
        public array $grants,
        public ?string $windowId = null,
    ) {}

    /**
     * @return  bool  True when the announcement is about one dated window rather than the holder's whole access.
     */
    public function isAboutWindow(): bool
    {
        return $this->windowId !== null;
    }
}
