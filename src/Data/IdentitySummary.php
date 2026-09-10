<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What the platform says about a person's account.
 */
final readonly class IdentitySummary
{
    /**
     * @param  string                $externalId   The platform's id for the account.
     * @param  string                $displayName  The name the platform reports.
     * @param  string|null           $username     The handle, when the platform has one.
     * @param  string|null           $avatarUrl    A picture, when the platform has one.
     * @param  array<string, mixed>  $meta         Anything else the connector wants kept with the identity.
     */
    public function __construct(
        public string $externalId,
        public string $displayName,
        public ?string $username = null,
        public ?string $avatarUrl = null,
        public array $meta = [],
    ) {}
}
