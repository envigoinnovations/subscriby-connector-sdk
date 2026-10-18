<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a connector shows the moment a turn is accepted, before any model has answered.
 */
final readonly class CopilotTurnOpening
{
    /**
     * @param  string       $threadId          The thread the turn belongs to.
     * @param  string       $thinkingSentence  The words to show while nothing has arrived yet, in the creator's language.
     * @param  string|null  $question          The creator's question to show above the answer, when they tapped it rather than typed it.
     */
    public function __construct(
        public string $threadId,
        public string $thinkingSentence,
        public ?string $question = null,
    ) {}
}
