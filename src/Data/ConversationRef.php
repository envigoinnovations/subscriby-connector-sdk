<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A support conversation between a project and one of its members.
 */
final readonly class ConversationRef
{
    /**
     * @param  string  $id         The conversation row's UUID.
     * @param  string  $projectId  The project whose inbox it belongs to.
     */
    public function __construct(
        public string $id,
        public string $projectId,
    ) {}
}
