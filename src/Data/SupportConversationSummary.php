<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\SupportConversationState;

/**
 * A support thread as a connector reads it back: where it is, who is on the other end, and where it stands.
 *
 * Enough for the connector to route an answer and to name the member in its
 * own reply ("Sent to Ada."), and, for a creator surface listing the inbox,
 * the state, the unread count, the last message and who holds it; the
 * messages themselves come through `Support::messagesOf()`, and the
 * creator's notes stay the core's.
 */
final readonly class SupportConversationSummary
{
    /**
     * @param  string                         $id             The thread row's UUID.
     * @param  string                         $projectId      The project whose inbox it is in.
     * @param  string                         $connector      The key of the connector the member wrote through.
     * @param  string                         $memberName     The member's name as the inbox shows it.
     * @param  SupportConversationState|null  $state          Where the thread stands, when the read carries it.
     * @param  int                            $unread         How many member messages nobody has read.
     * @param  DateTimeImmutable|null         $lastMessageAt  When the last message came.
     * @param  string|null                    $lastMessage    The last message's first words.
     * @param  string|null                    $assigneeName   Who on the team holds the thread, when someone does.
     * @param  string|null                    $memberId       The member's id in the project.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public string $connector,
        public string $memberName,
        public ?SupportConversationState $state = null,
        public int $unread = 0,
        public ?DateTimeImmutable $lastMessageAt = null,
        public ?string $lastMessage = null,
        public ?string $assigneeName = null,
        public ?string $memberId = null,
    ) {}

    /**
     * @return  ConversationRef  The thread, as the ports and the writes take it.
     */
    public function ref(): ConversationRef
    {
        return new ConversationRef($this->id, $this->projectId);
    }
}
