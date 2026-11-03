<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Whether a payment method takes real money or runs against the gateway's sandbox.
 *
 * A project may hold one method per gateway and mode, so a creator can keep
 * a sandbox method beside the live one while they test a checkout.
 */
enum PaymentMode: string
{
    use EnumHelpers;

    case Live = 'live';
    case Test = 'test';
}
