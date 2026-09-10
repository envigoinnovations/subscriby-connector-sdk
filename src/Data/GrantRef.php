<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\GrantMode;

/**
 * One member's access to one resource, as the ledger records it.
 *
 * `reference` is what the connector needs to revoke: the invite link for a
 * bearer grant, `guild:role:user` for a role, null while nothing has been
 * issued yet.
 */
final readonly class GrantRef
{
    /**
     * @param  string       $id         The grant row's UUID.
     * @param  GrantMode    $mode       How the access was given.
     * @param  string|null  $reference  The connector's handle on the grant, or null before issue.
     */
    public function __construct(
        public string $id,
        public GrantMode $mode,
        public ?string $reference = null,
    ) {}
}
