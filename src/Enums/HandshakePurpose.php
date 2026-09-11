<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * What a two-sided identity proof is for.
 *
 * One handshake shape serves every flow where a person proves they control an
 * account on a connector: a member linking a second connector, a creator
 * linking an account, a portal sign-in through a bot, a recovery relink after
 * a ban, and registering a backup identity ahead of one.
 */
enum HandshakePurpose: string
{
    use EnumHelpers;

    case MemberLink = 'member_link';
    case CreatorLink = 'creator_link';
    case PortalLogin = 'portal_login';
    case RecoveryRelink = 'recovery_relink';
    case BackupIdentity = 'backup_identity';

    /**
     * The prefix a deep-link payload carries for a handshake of this purpose, before the token.
     *
     * A connector's shared installation receives the payload (`/start
     * <prefix><token>` on Telegram) and routes on the prefix without reading
     * the row; recovery keeps the word its earlier flow already speaks.
     *
     * @return  string  The prefix, ending in an underscore.
     */
    public function startPayloadPrefix(): string
    {
        return match ($this) {
            self::MemberLink, self::CreatorLink => 'link_',
            self::PortalLogin => 'auth_',
            self::RecoveryRelink, self::BackupIdentity => 'recover_',
        };
    }
}
