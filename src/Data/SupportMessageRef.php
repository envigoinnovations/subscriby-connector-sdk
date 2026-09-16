<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A message the core recorded in a support thread, as a connector may refer to it.
 */
final readonly class SupportMessageRef
{
    /**
     * @param  string  $id              The message row's UUID.
     * @param  string  $conversationId  The thread it belongs to.
     * @param  string  $projectId       The project whose inbox the thread is in.
     */
    public function __construct(
        public string $id,
        public string $conversationId,
        public string $projectId,
    ) {}

    /**
     * @return  ConversationRef  The thread the message is in.
     */
    public function conversation(): ConversationRef
    {
        return new ConversationRef($this->conversationId, $this->projectId);
    }
}
