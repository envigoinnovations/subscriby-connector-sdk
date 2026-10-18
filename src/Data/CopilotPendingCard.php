<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A confirmation the Copilot paused on: the tool it wants to run and what it would run it with, in the creator's words.
 *
 * Every label is already translated by the core, so a connector lays the card
 * out in its own idiom and puts the id on its two buttons; the answer comes
 * back through `Copilot::decide()`.
 */
final readonly class CopilotPendingCard
{
    /**
     * @param  string                 $actionId       The card, for the two buttons.
     * @param  string                 $title          The heading, "Confirm before I do this" in the creator's language.
     * @param  string                 $toolLabel      The tool in words.
     * @param  array<string, string>  $argumentLines  What the model asked, one readable line per argument.
     * @param  string                 $note           What confirming and cancelling cost, in the creator's language.
     * @param  string                 $confirmLabel   The confirm button's words.
     * @param  string                 $cancelLabel    The cancel button's words.
     */
    public function __construct(
        public string $actionId,
        public string $title,
        public string $toolLabel,
        public array $argumentLines,
        public string $note,
        public string $confirmLabel,
        public string $cancelLabel,
    ) {}
}
