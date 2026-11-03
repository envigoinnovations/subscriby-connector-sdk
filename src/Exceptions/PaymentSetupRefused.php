<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * The core would not read or change a project's payment methods as asked.
 *
 * Carries a stable reason beside the core's translated sentence, so a
 * connector's creator surface can word the common refusals itself and
 * relay the rest.
 */
final class PaymentSetupRefused extends RuntimeException
{
    /** The ref names no creator. */
    public const string CREATOR_UNKNOWN = 'creator_unknown';

    /** The project is not one the creator may manage. */
    public const string PROJECT_UNKNOWN = 'project_unknown';

    /** The method is not one the creator may manage. */
    public const string METHOD_UNKNOWN = 'method_unknown';

    /** The gateway is none the core offers, or not one a method is configured from keys for. */
    public const string PROVIDER_UNKNOWN = 'provider_unknown';

    /** The draft failed the dashboard's own checks, or the write was refused for a reason the sentence names. */
    public const string INVALID = 'invalid';

    /** The project already holds a method for that gateway in that mode. */
    public const string ALREADY_CONFIGURED = 'already_configured';

    /** The creator may not change the project's payment methods. */
    public const string NOT_PERMITTED = 'not_permitted';

    /**
     * @param  string  $reason   One of the constants above.
     * @param  string  $message  The core's sentence, in the creator's language where the core has one.
     */
    private function __construct(
        public readonly string $reason,
        string $message,
    ) {
        parent::__construct($message);
    }

    /**
     * @param   string  $reason   One of the constants above.
     * @param   string  $message  The core's sentence.
     * @return  self    The exception.
     */
    public static function withReason(string $reason, string $message): self
    {
        return new self($reason, $message);
    }
}
