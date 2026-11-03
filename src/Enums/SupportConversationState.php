<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Where a support conversation stands, as the core keeps it.
 */
enum SupportConversationState: string
{
    use EnumHelpers;

    case Open = 'open';
    case Pending = 'pending';
    case Snoozed = 'snoozed';
    case Resolved = 'resolved';
}
