<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\DeliveryFailureKind;
use Subscriby\Connector\Enums\GrantMode;
use Subscriby\Connector\Enums\GrantState;

/**
 * Everything the core needs to write, or rewrite, one row of the access ledger.
 *
 * Handed to the Core API's `Grants::record()`, which is idempotent on the
 * subscription, the resource and the dated window: one purchase holds at most
 * one grant per resource per date, whatever the connector did to give it. A
 * `manual` resource has no connector and no identity.
 */
final readonly class GrantRecord
{
    /**
     * @param  string                    $subscriptionId  The purchase the grant belongs to.
     * @param  string                    $resourceId      The resource it admits to.
     * @param  GrantMode                 $mode            How the access was, or will be, given.
     * @param  GrantState                $state           Where the grant stands.
     * @param  string|null               $windowId        The dated window, or null for undated access.
     * @param  string|null               $identityId      The account admitted, or null while no identity is linked or for a hand-arranged perk.
     * @param  string|null               $connector       The connector key, or null for a hand-arranged perk.
     * @param  string|null               $reference       The connector's handle on the grant (an invite link, a role triple), or null before issue.
     * @param  array<string, mixed>      $payload         Anything the connector needs kept with the grant.
     * @param  DateTimeImmutable|null    $grantedAt       When access was given, or null when not yet.
     * @param  DateTimeImmutable|null    $revokedAt       When access was withdrawn, or null while live.
     * @param  DeliveryFailureKind|null  $failureKind     Why the last attempt failed, or null when it did not.
     * @param  string|null               $failureDetail   The sentence a creator reads about the failure.
     */
    public function __construct(
        public string $subscriptionId,
        public string $resourceId,
        public GrantMode $mode,
        public GrantState $state,
        public ?string $windowId = null,
        public ?string $identityId = null,
        public ?string $connector = null,
        public ?string $reference = null,
        public array $payload = [],
        public ?DateTimeImmutable $grantedAt = null,
        public ?DateTimeImmutable $revokedAt = null,
        public ?DeliveryFailureKind $failureKind = null,
        public ?string $failureDetail = null,
    ) {}
}
