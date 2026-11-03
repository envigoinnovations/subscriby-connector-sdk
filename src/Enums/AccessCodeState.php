<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Which of a plan's access codes a creator surface lists.
 *
 * The core keeps a code until it is redeemed, revoked or purged after its
 * expiry, so a list says which standing it wants: every code, the ones
 * still redeemable, the ones a member used, or the ones that ran out.
 */
enum AccessCodeState: string
{
    use EnumHelpers;

    case All = 'all';
    case Unredeemed = 'unredeemed';
    case Redeemed = 'redeemed';
    case Expired = 'expired';
}
