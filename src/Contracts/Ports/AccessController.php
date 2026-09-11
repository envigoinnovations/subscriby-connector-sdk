<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\GrantRef;
use Subscriby\Connector\Data\GrantRequest;
use Subscriby\Connector\Data\GrantResult;
use Subscriby\Connector\Data\GrantSnapshot;
use Subscriby\Connector\Data\IdentityRef;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\Membership;
use Subscriby\Connector\Data\ReconcileReport;
use Subscriby\Connector\Data\RevokeResult;
use Subscriby\Connector\Data\SpaceRef;
use Subscriby\Connector\Exceptions\UnsupportedByConnector;

/**
 * Giving and taking away access to a place.
 *
 * Bound by connectors that declare `access_control`. Every method is
 * idempotent: granting twice yields one grant, revoking what the platform
 * already lost reports revoked. A dated grant arrives with `opensAt`; a
 * connector whose manifest declares `early_admission_hold` may pre-issue it
 * and report it held, any other grants at opening; a held grant is released
 * through `admit()` when its window opens. A grant's reference is withdrawn
 * on its own through `revokeReference()` when another grant keeps the holder
 * in the place.
 */
interface AccessController
{
    /**
     * @param   InstallationRef  $installation  The installation acting.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   GrantRequest     $request       Who gets access to what.
     * @return  GrantResult      The reference to store, or the classified failure.
     */
    public function grant(InstallationRef $installation, CredentialBag $credentials, GrantRequest $request): GrantResult;

    /**
     * @param   InstallationRef  $installation  The installation acting.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   GrantRef         $grant         The grant being withdrawn.
     * @param   SpaceRef         $space         The place.
     * @param   IdentityRef      $identity      The account losing access.
     * @return  RevokeResult     Revoked, or the classified failure.
     */
    public function revoke(InstallationRef $installation, CredentialBag $credentials, GrantRef $grant, SpaceRef $space, IdentityRef $identity): RevokeResult;

    /**
     * Withdraw a grant's reference while its holder keeps their place.
     *
     * Called when another grant still covers the holder, or when the holder
     * owns the place: what was issued for this grant dies, the membership
     * stays. A connector whose grants carry no reference apart from the
     * membership itself answers revoked without a call.
     *
     * @param   InstallationRef  $installation  The installation acting.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   GrantRef         $grant         The grant whose reference is withdrawn.
     * @param   SpaceRef         $space         The place it was issued for.
     * @return  RevokeResult     Revoked, or the classified failure.
     */
    public function revokeReference(InstallationRef $installation, CredentialBag $credentials, GrantRef $grant, SpaceRef $space): RevokeResult;

    /**
     * Let the holder of a held grant in, now that its window has opened.
     *
     * @param   InstallationRef  $installation  The installation acting.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   GrantRef         $grant         The held grant.
     * @param   SpaceRef         $space         The place.
     * @param   IdentityRef      $identity      The account waiting at the door.
     * @return  GrantResult      Granted with the grant's reference, or the classified failure.
     *
     * @throws  UnsupportedByConnector  When the manifest declares no `early_admission_hold`.
     */
    public function admit(InstallationRef $installation, CredentialBag $credentials, GrantRef $grant, SpaceRef $space, IdentityRef $identity): GrantResult;

    /**
     * @param   InstallationRef  $installation  The installation asking.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   SpaceRef         $space         The place.
     * @param   IdentityRef      $identity      The account.
     * @return  Membership       The account's standing there right now.
     */
    public function membership(InstallationRef $installation, CredentialBag $credentials, SpaceRef $space, IdentityRef $identity): Membership;

    /**
     * Bring the platform into line with the ledger for one installation.
     *
     * @param   InstallationRef          $installation  The installation acting.
     * @param   CredentialBag            $credentials   Its secrets.
     * @param   iterable<GrantSnapshot>  $grants        Every grant the ledger holds for it, live and revoked.
     * @return  ReconcileReport          What was checked, re-given, taken away, and what could not be.
     */
    public function reconcile(InstallationRef $installation, CredentialBag $credentials, iterable $grants): ReconcileReport;
}
