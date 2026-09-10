<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * What a creator's linked identity is for.
 *
 * A creator holds at most one `Primary` and one `Backup` identity per
 * connector. The primary is the account that signs in and receives alerts;
 * the backup is registered ahead of a ban so a recovery can switch to it
 * without a fresh proof at the worst possible moment.
 */
enum IdentityPurpose: string
{
    use EnumHelpers;

    case Primary = 'primary';
    case Backup = 'backup';
}
