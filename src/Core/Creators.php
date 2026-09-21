<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\CreatorRegistration;
use Subscriby\Connector\Exceptions\RegistrationRefused;

/**
 * Creator accounts, as a connector may create them.
 *
 * A connector that declares `creator_registration` runs its own sign-up
 * conversation on its shared installation and hands the completed form here
 * with the account it was talking to; the core creates the email-first
 * account, links that account as the creator's primary identity and sends
 * the verification mail. The connector never sees the user beyond the ref.
 */
interface Creators
{
    /**
     * Create a creator account from a form a connector completed, linked to the account that filled it in.
     *
     * @param   CreatorRegistration  $registration  The form and the vouching account.
     * @return  CreatorRef           The new creator.
     *
     * @throws  RegistrationRefused  When the address already has an account, or the core would not adopt the account.
     */
    public function register(CreatorRegistration $registration): CreatorRef;

    /**
     * Whether an address already belongs to a creator, so a wizard can refuse it at the step it was typed.
     *
     * Compared without regard to case or surrounding whitespace, as
     * `register()` compares it: a connector that checked with its own
     * exact-match rule let a capitalised copy of an existing address through,
     * and a second account was opened under it.
     *
     * @param   string  $email  The address as the visitor typed it.
     * @return  bool    True when the address already has an account.
     */
    public function isEmailTaken(string $email): bool;
}
