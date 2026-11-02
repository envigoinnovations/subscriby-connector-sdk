<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\IdentityRecord;
use Subscriby\Connector\Enums\CreatorSignIn;

/**
 * Dashboard sessions, as a connector's own login door may open them.
 *
 * A connector whose platform can vouch for an account in the browser (an
 * OAuth2 flow, a login widget) offers its own "Continue with …" button on
 * the sign-in pages through the `CreatorLoginMethod` slot; its callback
 * hands the account here and acts on the answer. The core finds the creator
 * who holds the account and runs the login the password form runs, second
 * factor included. Nothing is recorded or linked on the way: an account no
 * creator holds is the connector's to send to the sign-up page with a
 * sealed token, so the account is adopted when the form is submitted.
 */
interface CreatorSessions
{
    /**
     * Sign in the creator who holds an account the platform just vouched for.
     *
     * @param   IdentityRecord  $identity  The account, of which the connector key and the platform's id are read.
     * @param   bool            $remember  Whether the session outlives the browser.
     * @return  CreatorSignIn   Signed in, parked for the second-factor challenge, or unknown when no creator holds the account.
     */
    public function signIn(IdentityRecord $identity, bool $remember = false): CreatorSignIn;
}
