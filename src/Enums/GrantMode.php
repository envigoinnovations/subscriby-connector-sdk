<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * How access to a resource is physically given.
 *
 * `BearerLink` is a one-member link the member must use (Telegram's invite
 * link, a single-use Discord invite). `Membership` adds the member to a space
 * directly. `Role` attaches a role or permission the member already-present
 * member gains (Discord). `CreatorTask` is access no API can give: the creator
 * is handed a task to do by hand, which is also what today's manual perk is
 * and what a WhatsApp group will need.
 */
enum GrantMode: string
{
    use EnumHelpers;

    case BearerLink = 'bearer_link';
    case Membership = 'membership';
    case Role = 'role';
    case CreatorTask = 'creator_task';

    /**
     * @return  bool  True when the grant's reference is a secret a holder could hand to someone else.
     */
    public function isBearer(): bool
    {
        return $this === self::BearerLink;
    }

    /**
     * @return  bool  True when the connector can grant and revoke without a person.
     */
    public function isAutomatic(): bool
    {
        return $this !== self::CreatorTask;
    }
}
