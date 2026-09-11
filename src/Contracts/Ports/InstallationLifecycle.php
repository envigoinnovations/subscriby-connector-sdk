<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\InstallationDraft;
use Subscriby\Connector\Data\InstallationHealth;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\InstallationRequest;
use Subscriby\Connector\Data\InstallationSummary;

/**
 * Connecting, verifying and disconnecting an installation on the platform.
 *
 * Required of every connector. The core owns the installation row and the
 * encrypted credentials; the connector owns every call to the platform.
 */
interface InstallationLifecycle
{
    /**
     * Start connecting: validate what the creator gave and say what happens next.
     *
     * @param   InstallationRequest  $request  The creator's answers to the install fields.
     * @return  InstallationDraft    Complete for a one-step connector; a URL or more fields otherwise.
     */
    public function begin(InstallationRequest $request): InstallationDraft;

    /**
     * Finish connecting: register with the platform and say what the installation is.
     *
     * @param   InstallationRequest  $request      The original request.
     * @param   InstallationDraft    $draft        The draft `begin()` returned, with any state the connector parked.
     * @param   CredentialBag        $credentials  The secrets the core will store, drawn from the request and the draft.
     * @return  InstallationSummary  What the platform says the installation is.
     */
    public function complete(InstallationRequest $request, InstallationDraft $draft, CredentialBag $credentials): InstallationSummary;

    /**
     * Ask the platform whether the installation still works.
     *
     * @param   InstallationRef     $installation  The installation.
     * @param   CredentialBag       $credentials   Its secrets.
     * @return  InstallationHealth  What the platform said.
     */
    public function verify(InstallationRef $installation, CredentialBag $credentials): InstallationHealth;

    /**
     * Withdraw the installation from the platform, best effort.
     *
     * @param  InstallationRef  $installation  The installation.
     * @param  CredentialBag    $credentials   Its secrets, which the platform may already have revoked.
     */
    public function disconnect(InstallationRef $installation, CredentialBag $credentials): void;

    /**
     * Refresh what the platform says the installation is.
     *
     * @param   InstallationRef      $installation  The installation.
     * @param   CredentialBag        $credentials   Its secrets.
     * @return  InstallationSummary  The current name, handle and picture.
     */
    public function describe(InstallationRef $installation, CredentialBag $credentials): InstallationSummary;

    /**
     * A link that starts a conversation with the installation, carrying a payload.
     *
     * @param   InstallationRef  $installation  The installation.
     * @param   string|null      $payload       What the conversation should open on (a plan id, a handshake token), or null for the home screen.
     * @return  string|null      The link, or null when the platform has no such thing.
     */
    public function startLink(InstallationRef $installation, ?string $payload = null): ?string;

    /**
     * The installation the connector runs for every creator, when its manifest declares the platform scope.
     *
     * Subscriby's own presence on the platform: the bot creators sign in
     * through, are asked in and receive their alerts from. The core hands it
     * back to `startLink()` for a creator's handshake links and to the
     * recovery facets that need the platform's own installation to act.
     *
     * @return  InstallationRef|null  The shared installation, or null for a connector installed per project only, or while none is configured.
     */
    public function platformInstallation(): ?InstallationRef;
}
