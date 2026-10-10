<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Where a creator stands in the Partner Program, as a connector reads it.
 *
 * `None` is a creator who never applied, so a connector shows the pitch and
 * the way to the application; `Applied` waits on the owner's review;
 * `Rejected` may apply again after the cooling period the summary names;
 * `Active` holds a code and earns; `Suspended` keeps the code and what was
 * earned but attributes and earns nothing.
 */
enum PartnerStanding: string
{
    use EnumHelpers;

    case None = 'none';
    case Applied = 'applied';
    case Active = 'active';
    case Rejected = 'rejected';
    case Suspended = 'suspended';

    /**
     * @return  bool  True when the owner approved the creator, whatever happened since.
     */
    public function isApproved(): bool
    {
        return $this === self::Active || $this === self::Suspended;
    }

    /**
     * @return  bool  True when the creator's link attributes and their referrals earn.
     */
    public function isEarning(): bool
    {
        return $this === self::Active;
    }
}
