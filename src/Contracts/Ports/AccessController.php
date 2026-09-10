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

/**
 * Giving and taking away access to a place.
 *
 * Bound by connectors that declare `access_control`. Every method is
 * idempotent: granting twice yields one grant, revoking what the platform
 * already lost reports revoked. A dated grant arrives with `opensAt`; a
 * connector whose manifest declares `early_admission_hold` may pre-issue it
 * and report it held, any other grants at opening.
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
