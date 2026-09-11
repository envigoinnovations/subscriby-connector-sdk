<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A completed creator sign-up form, as a connector's conversation collected it, and the account that filled it in.
 *
 * The connector has validated the form in its own idiom (a real address, a
 * password that meets the policy) and proven the account by talking to it;
 * the core creates the account and links the identity.
 */
final readonly class CreatorRegistration
{
    /**
     * @param  string          $name      The display name.
     * @param  string          $email     The address, unverified until the link is clicked.
     * @param  string          $password  The plaintext password, hashed by the core.
     * @param  IdentityRecord  $identity  The account the conversation ran with, as the connector describes it.
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public IdentityRecord $identity,
    ) {}
}
