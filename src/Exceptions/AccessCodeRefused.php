<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * The core would not redeem an access code.
 *
 * The reason is a stable word a connector keys its sentence on; the message
 * is the core's own, for the log.
 */
final class AccessCodeRefused extends RuntimeException
{
    /** The code is not one the project issued. */
    public const string UNKNOWN = 'unknown';

    /** The code was redeemed already. */
    public const string USED = 'used';

    /** The code's own expiry has passed. */
    public const string EXPIRED = 'expired';

    /** The member already holds a membership the code would clash with. */
    public const string ALREADY_MEMBER = 'already_member';

    /** The account is nobody's in the project, so there is no member to open the membership for. */
    public const string NOT_A_MEMBER = 'not_a_member';

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
