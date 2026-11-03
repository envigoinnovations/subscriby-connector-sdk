<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * A coupon read or write the core would not make for a connector's creator surface.
 *
 * Raised by every `Core\Coupons` call, so a connector reads one refusal
 * whatever the core's own exception was. The reason is a stable key a
 * connector words in its own language: `creator_unknown`, `project_unknown`,
 * `coupon_unknown`, `invalid` (the code taken or malformed, a percentage
 * over a hundred, a fixed discount without a currency, a plan of another
 * project; the message is the core's translated sentence), `tier_required`
 * (coupons are not on the creator's plan) and `not_permitted`.
 */
final class CouponRefused extends RuntimeException
{
    /** The ref names no creator. */
    public const string CREATOR_UNKNOWN = 'creator_unknown';

    /** No project the creator may manage has the id. */
    public const string PROJECT_UNKNOWN = 'project_unknown';

    /** No coupon the creator may manage has the id. */
    public const string COUPON_UNKNOWN = 'coupon_unknown';

    /** A field the core refused; the message is the core's translated sentence. */
    public const string INVALID = 'invalid';

    /** Coupons are not on the creator's plan. */
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
