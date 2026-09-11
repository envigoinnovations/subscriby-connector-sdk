<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;
use Subscriby\Connector\Data\DeliveryFailure;

/**
 * The platform, or the connector's own records, would not take an installation's credentials.
 *
 * Two reasons the core tells apart: the credentials already belong to an
 * installation the connector knows (a creator pasting a token twice), and
 * the platform refusing them (a token the platform revoked, a network that
 * would not answer). The core turns the first into its own refusal and
 * carries the second as the platform's classified word.
 */
final class InstallationRefused extends RuntimeException
{
    /** The credentials already belong to an installation the connector holds. */
    public const string CREDENTIALS_HELD = 'credentials_held';

    /** The platform refused the credentials, or could not be asked. */
    public const string PLATFORM_REFUSED = 'platform_refused';

    /**
     * @param  string                $message  What happened, for the log.
     * @param  string                $reason   One of this class's reason constants.
     * @param  DeliveryFailure|null  $failure  The platform's classified refusal, when it was the platform refusing.
     */
    private function __construct(
        string $message,
        public readonly string $reason,
        public readonly ?DeliveryFailure $failure = null,
    ) {
        parent::__construct($message);
    }

    /**
     * @param   string  $connector  The connector key.
     * @return  self    The refusal.
     */
    public static function credentialsHeld(string $connector): self
    {
        return new self(sprintf('Connector "%s" already holds an installation with these credentials.', $connector), self::CREDENTIALS_HELD);
    }

    /**
     * @param   string           $connector  The connector key.
     * @param   DeliveryFailure  $failure    Why the platform refused.
     * @return  self             The refusal.
     */
    public static function byPlatform(string $connector, DeliveryFailure $failure): self
    {
        return new self(sprintf('Connector "%s" could not install with these credentials (%s): %s', $connector, $failure->kind->value, $failure->detail), self::PLATFORM_REFUSED, $failure);
    }

    /**
     * @return  bool  True when the credentials are already held, which is the creator's mistake rather than the platform's refusal.
     */
    public function isCredentialsHeld(): bool
    {
        return $this->reason === self::CREDENTIALS_HELD;
    }
}
