<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;
use Subscriby\Connector\Enums\HandshakePurpose;
use Throwable;

/**
 * The core would not complete a handshake with the account a connector offered.
 *
 * Three reasons a connector tells its person apart: the token names no
 * pending handshake (expired, used, cancelled or never issued), the account
 * already belongs to someone else, and a purpose the core does not complete
 * through this call yet. The reason is a stable word; the sentence is the
 * connector's to write in its own idiom.
 */
final class HandshakeRefused extends RuntimeException
{
    /** The token names no pending handshake: expired, already used, cancelled or never issued. */
    public const string EXPIRED = 'expired';

    /** The account already signs in as another person. */
    public const string IDENTITY_HELD = 'identity_held';

    /** The handshake is for something this call does not complete. */
    public const string PURPOSE_NOT_SUPPORTED = 'purpose_not_supported';

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
    public static function expired(): self
    {
        return new self('The token names no pending handshake.', self::EXPIRED);
    }

    /**
     * @param   Throwable|null  $previous  The core's own refusal, when there was one.
     * @return  self            The refusal.
     */
    public static function identityHeld(?Throwable $previous = null): self
    {
        return new self('The account already belongs to another person.', self::IDENTITY_HELD, $previous);
    }

    /**
     * @param   HandshakePurpose  $purpose  What the handshake was for.
     * @return  self              The refusal.
     */
    public static function purposeNotSupported(HandshakePurpose $purpose): self
    {
        return new self(sprintf('A "%s" handshake is not completed through the identities API.', $purpose->value), self::PURPOSE_NOT_SUPPORTED);
    }

    /**
     * @return  bool  True when the token named no pending handshake.
     */
    public function isExpired(): bool
    {
        return $this->reason === self::EXPIRED;
    }

    /**
     * @return  bool  True when the account already belongs to another person.
     */
    public function isIdentityHeld(): bool
    {
        return $this->reason === self::IDENTITY_HELD;
    }
}
