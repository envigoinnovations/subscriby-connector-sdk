<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * The door a referred member came through, as a connector reports it.
 *
 * A connector sees two doors: its own deep link, which carries the affiliate's
 * code in the start payload, and a code the member typed when the checkout
 * asked for one. The core keeps a third, the portal link, which no connector
 * ever reports.
 */
enum ReferralSource: string
{
    use EnumHelpers;

    /**
     * The member opened the connector's deep link carrying the affiliate's code.
     */
    case ConnectorLink = 'connector_link';

    /**
     * The member typed the affiliate's code when the checkout asked for a code.
     */
    case CodeAtCheckout = 'code_at_checkout';
}
