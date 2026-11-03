<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * The core would not change a membership the way a member asked.
 *
 * The reason is a stable word a connector keys its sentence on; the message
 * is the core's own, for the log.
 */
final class MembershipRefused extends RuntimeException
{
    /** The account is nobody's in the project. */
    public const string NOT_A_MEMBER = 'not_a_member';

    /** The member holds no live membership there. */
    public const string SUBSCRIPTION_UNKNOWN = 'subscription_unknown';

    /** The membership is not one the member may cancel: already cancelled, a pass, or not theirs. */
    public const string NOT_CANCELLABLE = 'not_cancellable';

    /** The address, or the write, failed the portal's own checks; the sentence says which. */
    public const string INVALID = 'invalid';

    /** The member has no recovery email to resend or remove. */
    public const string NO_EMAIL = 'no_email';

    /**
     * @param  string  $reason   One of this class's reason constants.
     * @param  string  $message  What happened, for the log.
     */
    private function __construct(
        public readonly string $reason,
        string $message,
    ) {
        parent::__construct($message);
    }

    /**
     * @param   string  $reason   One of this class's reason constants.
     * @param   string  $message  What happened, for the log.
     * @return  self    The refusal.
     */
    public static function withReason(string $reason, string $message): self
    {
        return new self($reason, $message);
    }
}
