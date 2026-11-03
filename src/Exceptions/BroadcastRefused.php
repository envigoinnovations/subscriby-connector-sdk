<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * A broadcast the core would not size or send for a connector's creator surface.
 *
 * Raised by every `Core\Broadcasts` call, so a connector reads one refusal
 * whatever the core's own exception was. The reason is a stable key a
 * connector words in its own language: `creator_unknown`, `project_unknown`,
 * `no_connection` (the project runs no live installation to send through),
 * `invalid` (an empty or oversized body, an audience the core does not
 * know, a plan the audience cannot take, a pass audience without its
 * window; the message is the core's translated sentence), `tier_required`
 * (a pass audience without the passes capability) and `not_permitted`.
 */
final class BroadcastRefused extends RuntimeException
{
    /** The ref names no creator. */
    public const string CREATOR_UNKNOWN = 'creator_unknown';

    /** No project the creator may manage has the id. */
    public const string PROJECT_UNKNOWN = 'project_unknown';

    /** The project runs no live installation to send through. */
    public const string NO_CONNECTION = 'no_connection';

    /** A field the core refused; the message is the core's translated sentence. */
    public const string INVALID = 'invalid';

    /** The creator's plan does not allow this audience. */
    public const string TIER_REQUIRED = 'tier_required';

    /** The creator may not broadcast on the project. */
    public const string NOT_PERMITTED = 'not_permitted';

    /**
     * @param  string  $reason   The stable key.
     * @param  string  $message  What was refused, in one sentence.
     */
    private function __construct(
        public readonly string $reason,
        string $message,
    ) {
        parent::__construct($message);
    }

    /**
     * @param   string  $reason   The stable key the core's refusal carries.
     * @param   string  $message  What was refused, in one sentence.
     * @return  self    The exception.
     */
    public static function withReason(string $reason, string $message): self
    {
        return new self($reason, $message);
    }
}
