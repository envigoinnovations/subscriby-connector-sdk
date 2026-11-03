<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * The core would not list or settle a creator task as asked.
 */
final class CreatorTasksRefused extends RuntimeException
{
    /** The ref names no creator. */
    public const string CREATOR_UNKNOWN = 'creator_unknown';

    /** The project is not one the creator may manage. */
    public const string PROJECT_UNKNOWN = 'project_unknown';

    /** The task is not one the creator may manage. */
    public const string TASK_UNKNOWN = 'task_unknown';

    /** The task is already done, or the purchase behind it has ended. */
    public const string INVALID = 'invalid';

    /** The creator may not settle the project's tasks. */
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
