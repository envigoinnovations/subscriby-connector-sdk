<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;
use Throwable;

/**
 * The core would not create a creator account from a connector's sign-up form.
 *
 * Two reasons a connector tells its person apart: the address already has an
 * account (send them to sign in), and the core refusing the vouching account
 * (it belongs to someone else, or the connector runs no shared installation
 * to adopt it on). The reason is a stable word; the sentence is the
 * connector's.
 */
final class RegistrationRefused extends RuntimeException
{
    /** The address already has a creator account. */
    public const string EMAIL_TAKEN = 'email_taken';

    /** The core refused the vouching account. */
    public const string ACCOUNT_REFUSED = 'account_refused';

    /**
     * @param  string          $message   What happened, for the log.
     * @param  string          $reason    One of this class's reason constants.
     * @param  Throwable|null  $previous  The core's own refusal, when there was one.
     */
    private function __construct(
        string $message,
        public readonly string $reason,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * @return  self  The refusal.
     */
    public static function emailTaken(): self
    {
        return new self('The address already has a creator account.', self::EMAIL_TAKEN);
    }

    /**
     * @param   Throwable  $previous  The core's own refusal.
     * @return  self       The refusal.
     */
    public static function accountRefused(Throwable $previous): self
    {
        return new self('The core would not adopt the vouching account: '.$previous->getMessage(), self::ACCOUNT_REFUSED, $previous);
    }

    /**
     * @return  bool  True when the address already has an account.
     */
    public function isEmailTaken(): bool
    {
        return $this->reason === self::EMAIL_TAKEN;
    }
}
