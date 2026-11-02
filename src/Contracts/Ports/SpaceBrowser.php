<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\SpaceSummary;

/**
 * Listing the places an installation could gate, so a creator picks one from a list instead of asking the platform.
 *
 * Bound by connectors that declare `space_browsing`. A platform whose
 * installation can enumerate its places (a server's roles and channels)
 * lets the dashboard, the REST API and the apps offer a picker; one that
 * cannot (a bot that only learns of a chat when it is told) keeps to the
 * `SpaceCatalog` request. A connector may bind both: the request serves the
 * platform's own surface, the browser serves everything else.
 */
interface SpaceBrowser
{
    /**
     * @param   InstallationRef     $installation  The installation that would administer the place.
     * @param   CredentialBag       $credentials   Its secrets.
     * @param   string              $kind          The kind wanted, one the manifest declares.
     * @return  list<SpaceSummary>  Every place of that kind the installation can see, in the platform's own order.
     */
    public function browse(InstallationRef $installation, CredentialBag $credentials, string $kind): array;
}
