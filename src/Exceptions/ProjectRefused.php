<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * A project read or write the core would not make for a connector's creator surface.
 *
 * Raised by every `Core\Projects` call, so a connector reads one refusal
 * whatever the core's own exception was. The reason is a stable key a
 * connector words in its own language: `creator_unknown` (the ref names no
 * creator), `project_unknown` (no project the creator may manage has the
 * id, which is also what a project they may not see answers), `invalid`
 * (a field the core refused; the message is the core's translated
 * sentence), `tier_required` (the creator's plan allows no more projects,
 * or not this setting) and `not_permitted` (a teammate without the
 * permission). The message is for the developer, or for the creator when
 * the reason says so.
 */
final class ProjectRefused extends RuntimeException
{
    /** The ref names no creator. */
    public const string CREATOR_UNKNOWN = 'creator_unknown';

    /** No project the creator may manage has the id. */
    public const string PROJECT_UNKNOWN = 'project_unknown';

    /** A field the core refused; the message is the core's translated sentence. */
    public const string INVALID = 'invalid';

    /** The creator's plan allows no more projects, or not this setting. */
    public const string TIER_REQUIRED = 'tier_required';

    /** The creator may not do this on the project. */
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
