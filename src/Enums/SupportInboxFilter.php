<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Which conversations a creator surface lists from the inbox.
 *
 * The same cuts the dashboard's inbox offers: what still needs an answer,
 * what has unread member messages, the states one by one, or everything.
 */
enum SupportInboxFilter: string
{
    use EnumHelpers;

    case Open = 'open';
    case Unread = 'unread';
    case Pending = 'pending';
    case Snoozed = 'snoozed';
    case Resolved = 'resolved';
    case All = 'all';
}
