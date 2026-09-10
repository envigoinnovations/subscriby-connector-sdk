<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Why a platform refused a call, in words the core can act on.
 *
 * Every platform has its own error vocabulary (Telegram's `bot was blocked`,
 * Discord's `50007`); the connector's failure classifier maps it here so the
 * core decides once whether to retry, back off, tell the creator or give up.
 * `Unreachable` and `TargetMissing` never recover on a retry; `RateLimited`
 * carries how long to wait; `Configuration` means the creator has to change
 * something; `Transient` is the network.
 */
enum DeliveryFailureKind: string
{
    use EnumHelpers;

    case Unreachable = 'unreachable';
    case NotPermitted = 'not_permitted';
    case TargetMissing = 'target_missing';
    case RateLimited = 'rate_limited';
    case Configuration = 'configuration';
    case Transient = 'transient';
    case Other = 'other';

    /**
     * @return  bool  True when trying the same call again could succeed.
     */
    public function isRetryable(): bool
    {
        return $this === self::RateLimited || $this === self::Transient;
    }

    /**
     * @return  bool  True when the creator, not the platform, has to fix something.
     */
    public function isCreatorActionable(): bool
    {
        return $this === self::NotPermitted || $this === self::Configuration;
    }
}
