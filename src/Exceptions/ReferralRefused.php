<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * A Referral Program write the core would not make for a connector.
 *
 * Raised by `Core\Referrals::join()` and `capture()`, so a connector reads one
 * refusal whatever the core's own exception was. The reason is a stable key
 * a connector words in its own language: `programme_inactive` (no live
 * programme), `customers_only` (the member holds no active membership),
 * `already_joined`, `code_not_valid` (unknown, pending or suspended, one
 * word on purpose so a stranger learns nothing about a member's standing),
 * `self_referral`, `not_eligible` (referred before, or paid before) and
 * `installation_unknown`. The message is for the developer, never for a
 * member.
 */
final class ReferralRefused extends RuntimeException
{
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

    /**
     * @param   string  $installationId  The installation id that names no project's installation.
     * @return  self    The exception.
     */
    public static function installationUnknown(string $installationId): self
    {
        return new self('installation_unknown', sprintf('No project installation has the id "%s"; a platform-wide installation runs no referral program.', $installationId));
    }
}
