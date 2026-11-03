<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * An access-code read or write the core would not make for a connector's creator surface.
 *
 * Raised by every `Core\AccessCodes` call, so a connector reads one refusal
 * whatever the core's own exception was. The reason is a stable key a
 * connector words in its own language: `creator_unknown`, `plan_unknown`
 * (no plan the creator may manage has the id), `code_unknown`, `invalid`
 * (a quantity outside the batch's bounds, a custom expiry without its
 * amount or unit, a plan whose project takes no access codes; the message
 * is the core's translated sentence), `batch_in_flight` (a batch for the
 * plan is still being generated) and `not_permitted`.
 */
final class AccessCodesRefused extends RuntimeException
{
    /** The ref names no creator. */
    public const string CREATOR_UNKNOWN = 'creator_unknown';

    /** No plan the creator may manage has the id. */
    public const string PLAN_UNKNOWN = 'plan_unknown';

    /** No code of the plan has the id. */
    public const string CODE_UNKNOWN = 'code_unknown';

    /** A field the core refused; the message is the core's translated sentence. */
    public const string INVALID = 'invalid';

    /** A batch for the plan is still being generated. */
    public const string BATCH_IN_FLIGHT = 'batch_in_flight';

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
