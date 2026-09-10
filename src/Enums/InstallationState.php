<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Where an installation stands between being asked for and being gone.
 *
 * `Pending` is installed on a project with no credentials yet. `Revoked` is
 * the platform's verdict (a deleted bot, a withdrawn token) and is never
 * reached by the creator's own action, which is `Disconnected`. An
 * installation row is never deleted on revoke: identities and spaces hang off
 * it and members' history would go with them.
 */
enum InstallationState: string
{
    use EnumHelpers;

    case Pending = 'pending';
    case Connected = 'connected';
    case Degraded = 'degraded';
    case Revoked = 'revoked';
    case Disconnected = 'disconnected';

    /**
     * @return  bool  True while the installation can act for its project.
     */
    public function isOperational(): bool
    {
        return $this === self::Connected || $this === self::Degraded;
    }
}
