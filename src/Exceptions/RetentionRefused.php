<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * The core declined a Revenue Recovery call, with a stable reason beside the sentence.
 *
 * A connector branches on `$reason` and prints `getMessage()` when it prints
 * anything. On the member side: `installation_unknown` (the ref names no
 * project), `member_unknown` (the account has never spoken to the project),
 * `subscription_unknown` (no live membership to cancel), `reason_unknown`
 * (a code the list does not carry), `offer_unavailable` (the project makes
 * none now, or it was taken already), `cancellation_unknown` (a row that is
 * not this member's) and `gateway_refused` (the gateway would not hold the
 * charge or apply the discount, the member left exactly as they were). On
 * the creator side: `project_unknown`, `forbidden`, `invalid_settings` and
 * `coupon_unknown`. The sentence is in the person's language where the
 * core has one.
 */
final class RetentionRefused extends RuntimeException
{
    /**
     * @param  string  $reason   The stable reason a connector may branch on.
     * @param  string  $message  The sentence.
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
     * @param   string  $installationId  The ref's id.
     * @return  self    The refusal for a ref that names no project.
     */
    public static function installationUnknown(string $installationId): self
    {
        return new self('installation_unknown', sprintf('No project installation has the id "%s"; a platform-wide installation has no members to keep.', $installationId));
    }

    /**
     * @return  self  The refusal for an account the project has no member for.
     */
    public static function memberUnknown(): self
    {
        return new self('member_unknown', 'The account that is talking is not a member of this project.');
    }

    /**
     * @return  self  The refusal for a member with no live membership to cancel.
     */
    public static function subscriptionUnknown(): self
    {
        return new self('subscription_unknown', 'The member holds no live membership to cancel.');
    }

    /**
     * @param   string  $reason  The code as received.
     * @return  self    The refusal for a code the reason list does not carry.
     */
    public static function reasonUnknown(string $reason): self
    {
        return new self('reason_unknown', sprintf('"%s" is not one of the cancellation reasons.', $reason));
    }
}
