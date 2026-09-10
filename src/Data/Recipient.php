<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * Where a message goes: an identity, optionally inside a thread the platform keeps.
 */
final readonly class Recipient
{
    /**
     * @param  IdentityRef  $identity  The account to reach.
     * @param  string|null  $threadId  A platform thread or topic to post into, when the conversation has one.
     */
    public function __construct(
        public IdentityRef $identity,
        public ?string $threadId = null,
    ) {}
}
