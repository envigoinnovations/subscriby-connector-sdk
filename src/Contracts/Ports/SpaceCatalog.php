<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\SpaceAccess;
use Subscriby\Connector\Data\SpaceRef;
use Subscriby\Connector\Data\SpaceSummary;
use Subscriby\Connector\Enums\LinkPurpose;

/**
 * The places an installation can gate, and whether it controls them.
 *
 * Linking is asked for through the connector because only the platform can
 * prove the installation administers a place: Telegram answers a RequestChat
 * keyboard, Discord a guild pick. The connector reports the chosen place back
 * through its inbound gateway and the core files it under the purpose.
 */
interface SpaceCatalog
{
    /**
     * Ask the creator, in the connector's own idiom, to pick a place.
     *
     * @param  InstallationRef  $installation  The installation that will administer it.
     * @param  CredentialBag    $credentials   Its secrets.
     * @param  CreatorRef       $creator       Who is asked.
     * @param  string           $kind          The kind of place wanted, one the manifest declares.
     * @param  LinkPurpose      $purpose       What the place will be used for.
     */
    public function requestLink(InstallationRef $installation, CredentialBag $credentials, CreatorRef $creator, string $kind, LinkPurpose $purpose): void;

    /**
     * @param   InstallationRef  $installation  The installation to ask through.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   SpaceRef         $space         The place.
     * @return  SpaceSummary     What the platform says about it.
     */
    public function describe(InstallationRef $installation, CredentialBag $credentials, SpaceRef $space): SpaceSummary;

    /**
     * Whether the installation can grant and revoke in a place, and if not why.
     *
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   SpaceRef         $space         The place.
     * @return  SpaceAccess      Ready, or the creator-readable reason it is not.
     */
    public function diagnose(InstallationRef $installation, CredentialBag $credentials, SpaceRef $space): SpaceAccess;
}
