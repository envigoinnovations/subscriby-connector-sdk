<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Whether an installation acts, or waits to take over.
 *
 * A standby is registered with the platform for nothing until a failover
 * writes its credential into the live slot, so an idle installation never
 * answers updates and cannot be confused with the one members talk to.
 */
enum InstallationRole: string
{
    use EnumHelpers;

    case Live = 'live';
    case Standby = 'standby';
}
