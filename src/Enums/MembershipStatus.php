<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * A member's standing in a space, as the platform reports it.
 *
 * `Owner` matters on its own: the access remover must never evict the creator
 * from their own room, and attendance counts the host as present.
 */
enum MembershipStatus: string
{
    use EnumHelpers;

    case Owner = 'owner';
    case Administrator = 'administrator';
    case Member = 'member';
    case Restricted = 'restricted';
    case Left = 'left';
    case Banned = 'banned';
    case Unknown = 'unknown';

    /**
     * @return  bool  True when the person is inside the space right now.
     */
    public function isPresent(): bool
    {
        return match ($this) {
            self::Owner, self::Administrator, self::Member => true,
            self::Restricted, self::Left, self::Banned, self::Unknown => false,
        };
    }
}
