<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A completed creator sign-up form, as a connector's conversation collected it, and the account that filled it in.
 *
 * The connector has validated the form in its own idiom (a real address, a
 * password that meets the policy) and proven the account by talking to it;
 * the core creates the account and links the identity. A partner code the
 * conversation was opened with (a deep link carrying it) rides along, so
 * the core records the referral the way the sign-up page does for a typed
 * code; an unknown or suspended code never stops the registration.
 */
final readonly class CreatorRegistration
{
    /**
     * @param  string          $name         The display name.
     * @param  string          $email        The address, unverified until the link is clicked.
     * @param  string          $password     The plaintext password, hashed by the core.
     * @param  IdentityRecord  $identity     The account the conversation ran with, as the connector describes it.
     * @param  string|null     $partnerCode  The Partner Program code the conversation was opened with, or null.
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public IdentityRecord $identity,
        public ?string $partnerCode = null,
    ) {}
}
