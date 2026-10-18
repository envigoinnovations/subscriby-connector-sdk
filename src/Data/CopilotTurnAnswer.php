<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A finished turn as the chat shows it: the answer, where its work opens, the follow-ups and the card waiting on the creator.
 *
 * The links are URL buttons the core already signed, so a tap lands the
 * creator in the dashboard signed in; the suggestions are the words the
 * creator would type next, for the connector to put on buttons of its own
 * and send back through `Copilot::ask()`.
 */
final readonly class CopilotTurnAnswer
{
    /**
     * @param  string                   $markdown          The answer as Markdown, the follow-up block already removed.
     * @param  string                   $html              The same answer as canonical HTML.
     * @param  list<MessageAction>      $links             Where the turn's work opens in the dashboard, each a URL button.
     * @param  list<string>             $suggestions       The follow-up requests the answer offered, at most three.
     * @param  CopilotPendingCard|null  $card              The confirmation the turn paused on, or null.
     * @param  bool                     $changedSomething  True when a tool that is not read-only ran.
     * @param  string|null              $question          The question to repeat above the answer, when the creator tapped it rather than typed it.
     */
    public function __construct(
        public string $markdown,
        public string $html,
        public array $links = [],
        public array $suggestions = [],
        public ?CopilotPendingCard $card = null,
        public bool $changedSomething = false,
        public ?string $question = null,
    ) {}
}
