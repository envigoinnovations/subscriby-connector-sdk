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

    /** The ref names no creator. */
    public const string CREATOR_UNKNOWN = 'creator_unknown';

    /** The ref names no installation. */
    public const string INSTALLATION_UNKNOWN = 'installation_unknown';

    /** The installation belongs to no project the creator may manage. */
    public const string PROJECT_UNKNOWN = 'project_unknown';

    /** The installation cannot act right now. */
    public const string NOT_CONNECTED = 'not_connected';

    /** The connector offers no action by that key on the installation. */
    public const string ACTION_UNKNOWN = 'action_unknown';

    /** A field or an answer failed the rules the connector declared. */
    public const string INVALID = 'invalid';

    /** Whoever is acting may not change the project's connectors. */
    public const string NOT_PERMITTED = 'not_permitted';

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
     * @param   string  $reason   One of the constants above.
     * @param   string  $message  The core's sentence.
     * @return  self    The exception.
     */
    public static function withReason(string $reason, string $message): self
    {
        return new self($message, $reason);
    }

    /**
     * @return  bool  True when the credentials are already held, which is the creator's mistake rather than the platform's refusal.
     */
    public function isCredentialsHeld(): bool
    {
        return $this->reason === self::CREDENTIALS_HELD;
    }
}
