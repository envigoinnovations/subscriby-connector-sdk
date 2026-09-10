<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\GrantRecord;
use Subscriby\Connector\Data\GrantRef;
use Subscriby\Connector\Data\GrantSummary;

/**
 * The access ledger, as a connector may read and write it.
 *
 * The ledger is the core's record of who should be where; the connector holds
 * the platform's side (the invite link, the role). A connector reads by the
 * reference it issued, which is how a join request finds the purchase behind
 * the link it arrived on, and writes through `record()` when it moves legacy
 * grants across.
 */
interface Grants
{
    /**
     * @param   string             $id  The grant row's UUID.
     * @return  GrantSummary|null  The grant, or null when none has that id.
     */
    public function find(string $id): ?GrantSummary;

    /**
     * @param   string             $connector  The connector key.
     * @param   string             $reference  The connector's handle on the grant.
     * @return  GrantSummary|null  The grant that reference belongs to, or null when the connector never issued it.
     */
    public function findByReference(string $connector, string $reference): ?GrantSummary;

    /**
     * Write a grant, or rewrite the one the purchase already holds for that resource and date.
     *
     * Idempotent on the subscription, the resource and the window.
     *
     * @param   GrantRecord  $record  What the ledger should say.
     * @return  GrantRef     The row, new or updated.
     */
    public function record(GrantRecord $record): GrantRef;
}
