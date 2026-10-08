<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Where a member stands in a project's Referral Program, as a connector reads it.
 *
 * `Pending` exists only on programmes whose creator approves each affiliate;
 * a suspended affiliate keeps their code and what they earned but attributes
 * and earns nothing until the creator approves them again.
 */
enum AffiliateStatus: string
{
    use EnumHelpers;

    case Pending = 'pending';
    case Approved = 'approved';
    case Suspended = 'suspended';

    /**
     * @return  bool  True when the affiliate's links attribute and their referrals earn.
     */
    public function isEarning(): bool
    {
        return $this === self::Approved;
    }
}
