<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\PortalLoginMethod;
use Subscriby\Connector\Data\HandshakeRef;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\PortalLoginButton;
use Subscriby\Connector\Data\PortalLoginStart;
use Subscriby\Connector\Data\ProjectRef;

/**
 * A portal sign-in that hands back a link on the fake host carrying the handshake token.
 */
final class FakePortalLoginMethod implements PortalLoginMethod
{
    /**
     * @return  PortalLoginButton  The sheet entry.
     */
    public function button(): PortalLoginButton
    {
        return new PortalLoginButton('Continue with Fake', 'sparkles');
    }

    /**
     * @param   InstallationRef   $installation  The project's installation.
     * @param   ProjectRef        $project       The project.
     * @param   HandshakeRef      $handshake     The open handshake.
     * @return  PortalLoginStart  A link carrying the token and the code to type instead.
     */
    public function begin(InstallationRef $installation, ProjectRef $project, HandshakeRef $handshake): PortalLoginStart
    {
        return new PortalLoginStart(
            token: $handshake->token,
            url: 'https://fake.test/login?token='.$handshake->token,
            instructions: 'Type '.$handshake->token.' into the fake bot.',
        );
    }
}
