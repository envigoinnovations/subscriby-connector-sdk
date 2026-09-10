<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * How an identity came to be linked to a person.
 *
 * The source is kept on the link row because it decides what the link proves:
 * a `Bot` link was made by the account talking to the installation, a `Portal`
 * or `Handshake` link by a two-sided proof, an `Adopted` link by a member's own
 * tap on the portal to reuse an account from a sibling project, and a
 * `Backfill` link by the migration that read the legacy columns. Only the
 * backfill rows may be rewritten by a later backfill run.
 */
enum IdentityLinkSource: string
{
    use EnumHelpers;

    case Bot = 'bot';
    case Portal = 'portal';
    case Handshake = 'handshake';
    case Adopted = 'adopted';
    case Backfill = 'backfill';

    /**
     * @return  bool  True when the link came from a migration rather than from the person.
     */
    public function isBackfill(): bool
    {
        return $this === self::Backfill;
    }
}
