<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What came of asking a connector to take access away.
 *
 * A revoke of access the platform already lost (a member who left, a deleted
 * role) counts as revoked: the outcome is what matters, and a retry loop on
 * "nothing to revoke" would never end.
 */
final readonly class RevokeResult
{
    /**
     * @param  bool                  $revoked  True when the identity no longer has the access.
     * @param  DeliveryFailure|null  $failure  Why it could not be taken away, when it could not.
     */
    public function __construct(
        public bool $revoked,
        public ?DeliveryFailure $failure = null,
    ) {}

    /**
     * @return  self  A completed revocation.
     */
    public static function revoked(): self
    {
        return new self(true);
    }

    /**
     * @param   DeliveryFailure  $failure  Why the access could not be taken away.
     * @return  self             A failed revocation.
     */
    public static function failed(DeliveryFailure $failure): self
    {
        return new self(false, $failure);
    }
}
