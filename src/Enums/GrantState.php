<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * The life of one access grant.
 *
 * `PendingIdentity` is a purchase whose member has no identity on the grant's
 * connector yet; linking one materialises it. `Pending` is issued but not yet
 * confirmed by the platform. `Held` is a dated grant waiting for its window to
 * open (Telegram's held join request). `Failed` carries the connector's reason
 * so the creator can act on it.
 */
enum GrantState: string
{
    use EnumHelpers;

    case PendingIdentity = 'pending_identity';
    case Pending = 'pending';
    case Held = 'held';
    case Granted = 'granted';
    case Revoked = 'revoked';
    case Failed = 'failed';

    /**
     * @return  bool  True while the member either has, or is about to have, the access.
     */
    public function isLive(): bool
    {
        return match ($this) {
            self::Pending, self::Held, self::Granted => true,
            self::PendingIdentity, self::Revoked, self::Failed => false,
        };
    }
}
