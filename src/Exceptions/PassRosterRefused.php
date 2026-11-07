<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * The core would not read the Pass Roster as asked.
 */
final class PassRosterRefused extends RuntimeException
{
    /** The ref names no creator. */
    public const string CREATOR_UNKNOWN = 'creator_unknown';

    /** The project is not one the creator may manage. */
    public const string PROJECT_UNKNOWN = 'project_unknown';

    /** The plan is not one the creator may manage, or sells no dates. */
    public const string PLAN_UNKNOWN = 'plan_unknown';

    /** The date is not one the creator may manage. */
    public const string DATE_UNKNOWN = 'date_unknown';

    /** The pass is not one the creator may manage. */
    public const string PASS_UNKNOWN = 'pass_unknown';

    /** The creator may not read the project's members. */
    public const string NOT_PERMITTED = 'not_permitted';

    /**
     * @param  string  $reason   One of the constants above.
     * @param  string  $message  The core's sentence.
     */
    private function __construct(
        public readonly string $reason,
        string $message,
    ) {
        parent::__construct($message);
    }

    /**
     * @param   string  $reason   One of the constants above.
     * @param   string  $message  The core's sentence.
     * @return  self    The exception.
     */
    public static function withReason(string $reason, string $message): self
    {
        return new self($reason, $message);
    }
}
