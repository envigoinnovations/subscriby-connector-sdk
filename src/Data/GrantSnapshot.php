<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\GrantState;

/**
 * One grant with everything a connector needs to check or repair it.
 *
 * Handed to reconciliation and failover in bulk, so the connector can compare
 * the ledger's view of who should be where with the platform's.
 */
final readonly class GrantSnapshot
{
    /**
     * @param  GrantRef     $grant     The grant.
     * @param  SpaceRef     $space     The place it admits to.
     * @param  IdentityRef  $identity  The account it admits.
     * @param  GrantState   $state     What the ledger says the grant should be.
     */
    public function __construct(
        public GrantRef $grant,
        public SpaceRef $space,
        public IdentityRef $identity,
        public GrantState $state,
    ) {}
}
