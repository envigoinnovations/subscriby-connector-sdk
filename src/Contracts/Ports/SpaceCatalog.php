<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\IdentityRef;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\LinkRequest;
use Subscriby\Connector\Data\SpaceAccess;
use Subscriby\Connector\Data\SpaceRef;
use Subscriby\Connector\Data\SpaceSummary;
use Subscriby\Connector\Enums\LinkPurpose;

/**
 * The places an installation can gate, and whether it controls them.
 *
 * Linking is asked for through the connector because only the platform can
 * prove the installation administers a place: Telegram answers a RequestChat
 * keyboard, Discord a guild pick. The connector parks the request under the
 * creator's account with its own handle, reports the chosen place back
 * through its inbound gateway, and the core files it under the purpose. One
 * request per purpose is open at a time for a creator; asking again replaces it.
 */
interface SpaceCatalog
{
    /**
     * Ask the creator, in the connector's own idiom, to pick a place.
     *
     * @param  InstallationRef  $installation  The installation that will administer it.
     * @param  CredentialBag    $credentials   Its secrets.
     * @param  IdentityRef      $creator       The creator's account to ask in.
     * @param  LinkRequest      $request       What kind of place, what for, and for which project or resource.
     */
    public function requestLink(InstallationRef $installation, CredentialBag $credentials, IdentityRef $creator, LinkRequest $request): void;

    /**
     * Take back the request open for a purpose, and whatever prompt the platform still shows for it.
     *
     * @param  InstallationRef  $installation  The installation.
     * @param  CredentialBag    $credentials   Its secrets.
     * @param  IdentityRef      $creator       The creator's account the request was parked under.
     * @param  LinkPurpose      $purpose       Which request.
     */
    public function withdrawLinkRequest(InstallationRef $installation, CredentialBag $credentials, IdentityRef $creator, LinkPurpose $purpose): void;

    /**
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   IdentityRef      $creator       The creator's account.
     * @param   LinkPurpose      $purpose       Which request.
     * @return  string|null      The subject the open request was made for, or null when none is open.
     */
    public function pendingLinkRequest(InstallationRef $installation, CredentialBag $credentials, IdentityRef $creator, LinkPurpose $purpose): ?string;

    /**
     * What the creator does on the platform once a request is open, in one or two plain sentences.
     *
     * The core's pages say that a request is waiting; only the connector
     * knows whether the creator taps a keyboard button, picks a guild or
     * approves a prompt, so the sentence that tells them is the connector's.
     *
     * @param   LinkPurpose  $purpose  Which request the sentence is for.
     * @return  string       The instruction, translated.
     */
    public function linkInstructions(LinkPurpose $purpose): string;

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
