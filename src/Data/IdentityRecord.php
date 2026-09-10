<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * Everything the core needs to write, or rewrite, one identity row.
 *
 * Handed to the Core API's `Identities::record()`, which is idempotent on the
 * connector key, the installation the account was seen through and the
 * platform's own id for the account. Whose identity it is (a creator's, a
 * member's) is a separate link the connector makes afterwards, because the
 * same account can be both.
 */
final readonly class IdentityRecord
{
    /**
     * @param  string                  $connector       The connector key.
     * @param  string                  $externalId      The platform's id for the account.
     * @param  string|null             $installationId  The installation the account was seen through, or null when the platform gives accounts one id everywhere.
     * @param  string|null             $displayName     The name the platform reports.
     * @param  string|null             $username        The handle, when the platform has one.
     * @param  string|null             $avatarUrl       A picture, when the platform has one.
     * @param  string|null             $storageRef      The connector's own row id for it, opaque to the core.
     * @param  array<string, mixed>    $meta            Anything else the connector wants kept with the identity.
     * @param  DateTimeImmutable|null  $lastSeenAt      When the account last acted, or null when unknown.
     */
    public function __construct(
        public string $connector,
        public string $externalId,
        public ?string $installationId = null,
        public ?string $displayName = null,
        public ?string $username = null,
        public ?string $avatarUrl = null,
        public ?string $storageRef = null,
        public array $meta = [],
        public ?DateTimeImmutable $lastSeenAt = null,
    ) {}
}
