<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\GrantMode;

/**
 * The core asking a connector to give one identity access to one place.
 *
 * `existing` is set on a reissue or a repair so the connector can revoke the
 * old reference first; `opensAt` is set for a dated grant, and a connector that
 * supports early admission may pre-issue and hold the grant until then.
 */
final readonly class GrantRequest
{
    /**
     * @param  SpaceRef                $space     The place.
     * @param  IdentityRef             $identity  The account to admit.
     * @param  GrantMode               $mode      How the manifest says places of this kind are granted.
     * @param  GrantRef|null           $existing  The grant being replaced, if any.
     * @param  DateTimeImmutable|null  $opensAt   When a dated grant becomes active, or null for immediate access.
     * @param  array<string, mixed>    $meta      Anything the core wants echoed back in the result.
     */
    public function __construct(
        public SpaceRef $space,
        public IdentityRef $identity,
        public GrantMode $mode,
        public ?GrantRef $existing = null,
        public ?DateTimeImmutable $opensAt = null,
        public array $meta = [],
    ) {}

    /**
     * @return  bool  True when the grant is for a date that has not opened yet.
     */
    public function isAheadOfOpening(): bool
    {
        return $this->opensAt !== null && $this->opensAt > new DateTimeImmutable;
    }
}
