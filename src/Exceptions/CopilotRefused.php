<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * The core declined a Copilot call, with a stable reason beside the sentence.
 *
 * A connector branches on `$reason` (`creator_unknown`, `not_allowed`,
 * `credit_exhausted`, `cap_reached`, `message_invalid`, `project_unknown`,
 * `thread_unknown`, `action_unknown`, `action_expired`, `surface_unavailable`)
 * and prints `getMessage()`, the core's sentence in the creator's language,
 * when it prints anything; a spent credit is the one refusal a connector
 * answers with a way to top up.
 */
final class CopilotRefused extends RuntimeException
{
    /**
     * @param  string  $reason   The stable reason a connector may branch on.
     * @param  string  $message  The sentence, in the creator's language where the core has one.
     */
    private function __construct(
        public readonly string $reason,
        string $message,
    ) {
        parent::__construct($message);
    }

    /**
     * @param   string  $reason   The stable reason.
     * @param   string  $message  The sentence.
     * @return  self    The refusal.
     */
    public static function withReason(string $reason, string $message): self
    {
        return new self($reason, $message);
    }

    /**
     * @param   string  $creatorId  The ref's id.
     * @return  self    The refusal for a ref that names no creator.
     */
    public static function creatorUnknown(string $creatorId): self
    {
        return new self('creator_unknown', sprintf('No creator has the id "%s".', $creatorId));
    }

    /**
     * @param   string  $actionId  The id as received.
     * @return  self    The refusal for a card the creator has no open one of.
     */
    public static function actionUnknown(string $actionId): self
    {
        return new self('action_unknown', sprintf('No confirmation of the creator\'s is open under the id "%s".', $actionId));
    }

    /**
     * @param   string  $message  The core's sentence.
     * @return  self    The refusal for a connector or installation through which no turn can be shown.
     */
    public static function surfaceUnavailable(string $message): self
    {
        return new self('surface_unavailable', $message);
    }
}
