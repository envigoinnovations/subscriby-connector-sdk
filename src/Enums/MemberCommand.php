<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * The things a member may ask a connector's bot to do from a button.
 *
 * The creator's side of the bot is the {@see ManagementCommand} catalogue;
 * this is the member's, and it is short on purpose: the core puts a button on
 * a message it sends a member so they can act without typing, and a connector
 * renders it into whatever its platform routes back to the member surface. A
 * connector with no member surface renders none of them and the button is
 * left off the message.
 */
enum MemberCommand: string
{
    use EnumHelpers;

    /**
     * Open the member's home in the bot: their memberships, plans and status.
     */
    case Start = 'start';

    /**
     * Mint fresh access grants for everything the member's subscriptions entitle them to.
     */
    case ReissueGrants = 'reissue_grants';
}
