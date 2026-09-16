<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A support thread as a connector reads it back: where it is, and who is on the other end.
 *
 * Enough for the connector to route an answer and to name the member in its
 * own reply ("Sent to Ada."); the messages, the status moves and the
 * creator's notes stay the core's.
 */
final readonly class SupportConversationSummary
{
    /**
     * @param  string  $id          The thread row's UUID.
     * @param  string  $projectId   The project whose inbox it is in.
     * @param  string  $connector   The key of the connector the member wrote through.
     * @param  string  $memberName  The member's name as the inbox shows it.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public string $connector,
        public string $memberName,
    ) {}

    /**
     * @return  ConversationRef  The thread, as the ports and the writes take it.
     */
    public function ref(): ConversationRef
    {
        return new ConversationRef($this->id, $this->projectId);
    }
}
