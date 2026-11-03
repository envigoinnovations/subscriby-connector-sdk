<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A member's recovery email as the portal keeps it: the address, and whether the member has verified it.
 *
 * A verified address lets the member sign in to the portal when they lose
 * the platform account they bought through; an unverified one waits for
 * the link the core mailed.
 */
final readonly class RecoveryEmailStanding
{
    /**
     * @param  string|null  $email     The address, or null while none is set.
     * @param  bool         $verified  Whether the member clicked the verification link.
     */
    public function __construct(
        public ?string $email,
        public bool $verified,
    ) {}

    /**
     * @return  bool  True when an address is set, verified or not.
     */
    public function isSet(): bool
    {
        return $this->email !== null && $this->email !== '';
    }
}
