<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\HandshakeRef;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\PortalLoginButton;
use Subscriby\Connector\Data\PortalLoginStart;
use Subscriby\Connector\Data\ProjectRef;

/**
 * Signing a member into the portal through the connector.
 *
 * Bound by connectors that declare `portal_login`. The core mints the
 * handshake and polls it; the connector says how the sign-in starts and
 * completes the handshake from its inbound side when the account presents the
 * token.
 */
interface PortalLoginMethod
{
    /**
     * @return  PortalLoginButton  How the option appears on the portal's sign-in sheet.
     */
    public function button(): PortalLoginButton;

    /**
     * @param   InstallationRef   $installation  The project's installation the member will talk to.
     * @param   ProjectRef        $project       The project whose portal it is.
     * @param   HandshakeRef      $handshake     The open handshake carrying the token to present.
     * @return  PortalLoginStart  Where the visitor goes, or what to do by hand.
     */
    public function begin(InstallationRef $installation, ProjectRef $project, HandshakeRef $handshake): PortalLoginStart;
}
