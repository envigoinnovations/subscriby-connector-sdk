<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * A Partner Program call the core would not make for a connector's creator surface.
 *
 * Raised by every method of `Core\PartnerProgram`, so a connector reads one
 * refusal whatever the core's own exception was. The reason is a stable key
 * a connector words in its own language: `creator_unknown` (the ref names
 * nobody), `not_partner` (the creator never applied), `not_approved` (they
 * applied and wait, were turned down, or are suspended), `forbidden`,
 * `commitment_unknown`, `invalid_details` (a draft the core could not
 * read), and the core's own refusals relayed with their reason
 * (`terms_not_accepted`, `payout_details_missing`, `bank_account_incomplete`,
 * `credit_rail_off`, `commitment_already_verified`, `commitment_already_held`,
 * `commitment_audience_unfit`, `commitment_audience_too_small`). The message
 * is the core's translated sentence, fit to relay to the creator as it is.
 */
final class PartnerRefused extends RuntimeException
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
}
