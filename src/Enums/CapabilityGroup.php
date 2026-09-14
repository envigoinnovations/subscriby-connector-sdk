<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * The shelf a capability sits on when a page lists what a connector can do.
 *
 * Fourteen capabilities read as a wall; four groups read as a story: how the
 * connector talks to members, how it lets them in, how it survives a ban,
 * and how it takes money. The dashboard's Configuration tab, the directory's
 * matrix and a connector's own pages shelve them the same way, so the
 * grouping is data on the capability rather than a layout decision in a view.
 */
enum CapabilityGroup: string
{
    use EnumHelpers;

    case Messaging = 'messaging';
    case Access = 'access';
    case Recovery = 'recovery';
    case Payments = 'payments';
}
