<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\GrantMode;

/**
 * What came of asking a connector to grant access.
 *
 * `reference` is what the core stores to revoke later; `held` means the grant
 * was pre-issued for a date and is waiting for it to open; `failure` carries
 * the classified reason when nothing was granted.
 */
final readonly class GrantResult
{
    /**
     * @param  bool                    $granted    True when the identity has, or will have at opening, the access.
     * @param  GrantMode               $mode       How it was given.
     * @param  string|null             $reference  The connector's handle on the grant, for revocation.
     * @param  DateTimeImmutable|null  $grantedAt  When the access took effect, null while held or failed.
     * @param  bool                    $held       True when pre-issued and waiting for a window to open.
     * @param  DeliveryFailure|null    $failure    Why nothing was granted, when it was not.
     */
    public function __construct(
        public bool $granted,
        public GrantMode $mode,
        public ?string $reference = null,
        public ?DateTimeImmutable $grantedAt = null,
        public bool $held = false,
        public ?DeliveryFailure $failure = null,
    ) {}

    /**
     * @param   GrantMode    $mode       How it was given.
     * @param   string|null  $reference  The connector's handle on the grant.
     * @return  self         An immediate grant.
     */
    public static function granted(GrantMode $mode, ?string $reference): self
    {
        return new self(true, $mode, $reference, new DateTimeImmutable);
    }

    /**
     * @param   GrantMode  $mode       How it will be given.
     * @param   string     $reference  The pre-issued handle the holder already carries.
     * @return  self       A grant held until its window opens.
     */
    public static function held(GrantMode $mode, string $reference): self
    {
        return new self(true, $mode, $reference, null, true);
    }

    /**
     * @param   GrantMode        $mode     How it would have been given.
     * @param   DeliveryFailure  $failure  Why it was not.
     * @return  self             A failed grant.
     */
    public static function failed(GrantMode $mode, DeliveryFailure $failure): self
    {
        return new self(false, $mode, null, null, false, $failure);
    }
}
