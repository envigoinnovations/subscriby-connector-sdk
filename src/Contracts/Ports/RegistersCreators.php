<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\InstallationRef;

/**
 * Creating a creator account from inside the connector.
 *
 * Bound by connectors that declare `creator_registration`. The connector runs
 * its own sign-up conversation on the platform installation and creates the
 * email-first account through the core's registration action, then links the
 * identity it was talking to; the core only needs to know where the flow
 * starts.
 */
interface RegistersCreators
{
    /**
     * @param   InstallationRef  $platform  The platform-scope installation that hosts the sign-up.
     * @return  string           A link that starts the sign-up conversation.
     */
    public function signupEntry(InstallationRef $platform): string;
}
