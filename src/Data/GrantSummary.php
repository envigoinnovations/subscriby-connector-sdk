<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\GrantMode;
use Subscriby\Connector\Enums\GrantState;

/**
 * One row of the access ledger as a connector may read it.
 *
 * What a join-request handler or a reconciliation needs to decide: whose
 * purchase the reference belongs to, which resource and date it opens, and
 * whether the ledger still says the access is live.
 */
final readonly class GrantSummary
{
    /**
     * @param  string                  $id              The grant row's UUID.
     * @param  string                  $projectId       The project the resource belongs to.
     * @param  string                  $subscriptionId  The purchase.
     * @param  string                  $resourceId      The resource.
     * @param  GrantMode               $mode            How the access was given.
     * @param  GrantState              $state           Where the grant stands.
     * @param  string|null             $windowId        The dated window, or null for undated access.
     * @param  string|null             $identityId      The account admitted, when one is linked.
     * @param  string|null             $connector       The connector key, or null for a hand-arranged perk.
     * @param  string|null             $reference       The connector's handle on the grant.
     * @param  DateTimeImmutable|null  $grantedAt       When access was given.
     * @param  DateTimeImmutable|null  $revokedAt       When access was withdrawn.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public string $subscriptionId,
        public string $resourceId,
        public GrantMode $mode,
        public GrantState $state,
        public ?string $windowId = null,
        public ?string $identityId = null,
        public ?string $connector = null,
        public ?string $reference = null,
        public ?DateTimeImmutable $grantedAt = null,
        public ?DateTimeImmutable $revokedAt = null,
    ) {}

    /**
     * @return  GrantRef  The ref a port takes.
     */
    public function ref(): GrantRef
    {
        return new GrantRef($this->id, $this->mode, $this->reference);
    }
}
