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
 * the link it arrived on, lists what a purchase holds to show a member their
 * links, writes through `record()` when it moves legacy grants across, and
 * moves a grant along when the platform tells it something the core cannot
 * see: a join request held at the door, a member let in.
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
     * The grants a purchase holds that admit, or are about to admit, its member.
     *
     * @param   string              $subscriptionId  The purchase.
     * @return  list<GrantSummary>  The `pending`, `held` and `granted` rows, oldest first.
     */
    public function listLiveForSubscription(string $subscriptionId): array;

    /**
     * Write a grant, or rewrite the one the purchase already holds for that resource and date.
     *
     * Idempotent on the subscription, the resource and the window.
     *
     * @param   GrantRecord  $record  What the ledger should say.
     * @return  GrantRef     The row, new or updated.
     */
    public function record(GrantRecord $record): GrantRef;

    /**
     * Record that the member is waiting at the door: they used their grant before its window opened and the platform holds the request.
     *
     * @param   string             $id  The grant row's UUID.
     * @return  GrantSummary|null  The grant, held, or null when none has that id.
     */
    public function markHeld(string $id): ?GrantSummary;

    /**
     * Record that the member is in: the platform admitted them on this grant.
     *
     * @param   string             $id  The grant row's UUID.
     * @return  GrantSummary|null  The grant, granted, or null when none has that id.
     */
    public function markGranted(string $id): ?GrantSummary;
}
