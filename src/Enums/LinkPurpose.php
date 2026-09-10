<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Why a creator is being asked to point the connector at a space.
 *
 * The same picker serves four questions: a new resource, the replacement of a
 * lost one, a standby kept ready for one, and the group support is relayed
 * into. The connector renders the ask; the core needs the answer filed under
 * the right purpose.
 */
enum LinkPurpose: string
{
    use EnumHelpers;

    case Resource = 'resource';
    case Replacement = 'replacement';
    case Standby = 'standby';
    case SupportRelay = 'support_relay';
}
