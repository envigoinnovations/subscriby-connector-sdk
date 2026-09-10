<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * The shelf a connector sits on in the directory.
 */
enum ListingCategory: string
{
    use EnumHelpers;

    case Messaging = 'messaging';
    case Community = 'community';
    case Payments = 'payments';
    case Productivity = 'productivity';
}
