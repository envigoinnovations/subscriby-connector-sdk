<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What the core answers when a turn is accepted: the thread it will run in, for the connector to park.
 *
 * The turn itself runs on the queue and reaches the chat through the
 * `CopilotSurface` port, so nothing of its outcome is here.
 */
final readonly class CopilotTurnTicket
{
    /**
     * @param  string  $threadId  The thread, new when the ask named none or named one the core no longer holds.
     */
    public function __construct(
        public string $threadId,
    ) {}
}
