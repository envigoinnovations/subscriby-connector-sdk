<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What the core did with a member's support message, and what the connector should say back.
 *
 * The auto-reply is the project's own acknowledgement, due on the first
 * message of a thread by the core's rules and null otherwise; the connector
 * renders and sends it, because the transport is the connector's and the
 * core never writes to a platform on its own.
 */
final readonly class IngestedSupportMessage
{
    /**
     * @param  SupportMessageRef  $message    The message as recorded, new or updated in place.
     * @param  string|null        $autoReply  The project's acknowledgement to send the member, or null when none is due.
     */
    public function __construct(
        public SupportMessageRef $message,
        public ?string $autoReply = null,
    ) {}
}
