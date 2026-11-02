<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A broadcast as a connector posts it once: the message, and the places whose holders it is for.
 *
 * The audience is named by resources rather than by people so the connector
 * can address it in its own idiom (a mention of the role behind each place)
 * without the core listing every member; an empty audience means everyone
 * the installation reaches.
 */
final readonly class Announcement
{
    /**
     * @param  Message            $message   The canonical message.
     * @param  list<ResourceRef>  $audience  The places whose holders it is for; empty for every member.
     * @param  string|null        $title     A heading the connector may show above the body, when the platform has one.
     */
    public function __construct(
        public Message $message,
        public array $audience = [],
        public ?string $title = null,
    ) {}

    /**
     * @return  bool  True when the announcement is for everyone rather than the holders of named places.
     */
    public function isForEveryone(): bool
    {
        return $this->audience === [];
    }
}
