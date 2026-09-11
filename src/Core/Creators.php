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
}
