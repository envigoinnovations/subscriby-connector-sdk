<?php

declare(strict_types=1);

namespace Subscriby\Connector\Exceptions;

use RuntimeException;

/**
 * The core would not start a hosted checkout.
 *
 * The reason is a stable word a connector keys its sentence on; the message
 * is the core's own, translated for the member where the refusal is theirs
 * to read (a coupon that cannot be honoured, a membership they already hold,
 * a gateway that turned the checkout down) and for the log otherwise.
 */
final class CheckoutRefused extends RuntimeException
{
    /** The installation serves no project the connector may act for. */
    public const string INSTALLATION_UNKNOWN = 'installation_unknown';

    /** The account is nobody's in the project, so there is no member to buy for. */
    public const string MEMBER_UNKNOWN = 'member_unknown';

    /** The plan is not the project's. */
    public const string PLAN_UNKNOWN = 'plan_unknown';

    /** The payment method is not the project's, or is switched off. */
    public const string METHOD_UNKNOWN = 'method_unknown';

    /** The method's rail has no hosted checkout the core can start from here: an access code, or a connector's own currency. */
    public const string PROVIDER_UNSUPPORTED = 'provider_unsupported';

    /** The member may not buy this plan now: they hold a live membership, or a checkout of theirs is still open. */
    public const string NOT_ELIGIBLE = 'not_eligible';

    /** The coupon the member carries cannot be honoured on this plan or rail. */
    public const string COUPON_REFUSED = 'coupon_refused';

    /** The gateway refused to open the checkout, or the method is misconfigured. */
    public const string GATEWAY_REFUSED = 'gateway_refused';

    /**
     * @param  string  $reason   One of this class's reason constants.
     * @param  string  $message  What happened: the core's sentence for the member, or a line for the log.
     */
    private function __construct(
        public readonly string $reason,
        string $message,
    ) {
        parent::__construct($message);
    }

    /**
     * @param   string  $reason   One of this class's reason constants.
     * @param   string  $message  What happened: the core's sentence for the member, or a line for the log.
     * @return  self    The refusal.
     */
    public static function withReason(string $reason, string $message): self
    {
        return new self($reason, $message);
    }
}
