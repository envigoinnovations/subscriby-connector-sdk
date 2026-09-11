<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use PHPUnit\Framework\Assert;
use Subscriby\Connector\Contracts\Ports\AccessController;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\DeliveryFailure;
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
use Subscriby\Connector\Enums\DeliveryFailureKind;
use Subscriby\Connector\Enums\GrantMode;
use Subscriby\Connector\Enums\MembershipStatus;
use Subscriby\Connector\Exceptions\UnsupportedByConnector;

/**
 * An access controller that keeps memberships in memory.
 *
 * Grants are memberships, never links, and an identity whose id starts with
 * `blocked-` cannot be reached, so both halves of the core's grant pipeline
 * can be exercised. Idempotent like the real thing: granting twice is one
 * membership, revoking a stranger is still revoked.
 */
final class FakeAccessController implements AccessController
{
    /** @var array<string, GrantMode> Memberships keyed `space|identity`. */
    public array $memberships = [];

    /**
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   GrantRequest     $request       Who gets access to what.
     * @return  GrantResult      Granted with a `membership:` reference, or unreachable for a `blocked-` identity.
     */
    public function grant(InstallationRef $installation, CredentialBag $credentials, GrantRequest $request): GrantResult
    {
        if (str_starts_with($request->identity->externalId, 'blocked-')) {
            return GrantResult::failed($request->mode, new DeliveryFailure(DeliveryFailureKind::Unreachable, 'The member blocked the fake bot.'));
        }

        $this->memberships[$this->key($request->space, $request->identity)] = $request->mode;

        return GrantResult::granted($request->mode, 'membership:'.$request->space->externalId.':'.$request->identity->externalId);
    }

    /**
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   GrantRef         $grant         The grant.
     * @param   SpaceRef         $space         The place.
     * @param   IdentityRef      $identity      The account.
     * @return  RevokeResult     Always revoked.
     */
    public function revoke(InstallationRef $installation, CredentialBag $credentials, GrantRef $grant, SpaceRef $space, IdentityRef $identity): RevokeResult
    {
        unset($this->memberships[$this->key($space, $identity)]);

        return RevokeResult::revoked();
    }

    /**
     * Memberships carry no reference apart from themselves, so there is nothing to withdraw.
     *
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   GrantRef         $grant         The grant.
     * @param   SpaceRef         $space         The place.
     * @return  RevokeResult     Always revoked, the membership untouched.
     */
    public function revokeReference(InstallationRef $installation, CredentialBag $credentials, GrantRef $grant, SpaceRef $space): RevokeResult
    {
        return RevokeResult::revoked();
    }

    /**
     * The fake holds nothing ahead of a window: its manifest declares no `early_admission_hold`.
     *
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   GrantRef         $grant         The grant.
     * @param   SpaceRef         $space         The place.
     * @param   IdentityRef      $identity      The account.
     * @return  GrantResult      Never.
     *
     * @throws  UnsupportedByConnector  Always.
     */
    public function admit(InstallationRef $installation, CredentialBag $credentials, GrantRef $grant, SpaceRef $space, IdentityRef $identity): GrantResult
    {
        throw UnsupportedByConnector::facet($installation->connector, 'early admission holds');
    }

    /**
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   SpaceRef         $space         The place.
     * @param   IdentityRef      $identity      The account.
     * @return  Membership       Member when granted here, left otherwise.
     */
    public function membership(InstallationRef $installation, CredentialBag $credentials, SpaceRef $space, IdentityRef $identity): Membership
    {
        return new Membership(isset($this->memberships[$this->key($space, $identity)]) ? MembershipStatus::Member : MembershipStatus::Left);
    }

    /**
     * @param   InstallationRef          $installation  The installation.
     * @param   CredentialBag            $credentials   Its secrets.
     * @param   iterable<GrantSnapshot>  $grants        The ledger's view.
     * @return  ReconcileReport          Memberships added for live grants and removed for dead ones.
     */
    public function reconcile(InstallationRef $installation, CredentialBag $credentials, iterable $grants): ReconcileReport
    {
        $checked = 0;
        $reasserted = 0;
        $revoked = 0;

        foreach ($grants as $snapshot) {
            $checked++;
            $key = $this->key($snapshot->space, $snapshot->identity);

            if ($snapshot->state->isLive() && ! isset($this->memberships[$key])) {
                $this->memberships[$key] = $snapshot->grant->mode;
                $reasserted++;
            }

            if (! $snapshot->state->isLive() && isset($this->memberships[$key])) {
                unset($this->memberships[$key]);
                $revoked++;
            }
        }

        return new ReconcileReport($checked, $reasserted, $revoked);
    }

    /**
     * @param  string  $spaceExternalId     The place.
     * @param  string  $identityExternalId  The account.
     */
    public function assertGranted(string $spaceExternalId, string $identityExternalId): void
    {
        Assert::assertArrayHasKey($spaceExternalId.'|'.$identityExternalId, $this->memberships, sprintf('Expected %s to hold access to %s.', $identityExternalId, $spaceExternalId));
    }

    /**
     * @param  string  $spaceExternalId     The place.
     * @param  string  $identityExternalId  The account.
     */
    public function assertNotGranted(string $spaceExternalId, string $identityExternalId): void
    {
        Assert::assertArrayNotHasKey($spaceExternalId.'|'.$identityExternalId, $this->memberships, sprintf('Expected %s not to hold access to %s.', $identityExternalId, $spaceExternalId));
    }

    /**
     * @param   SpaceRef     $space     The place.
     * @param   IdentityRef  $identity  The account.
     * @return  string       The membership key.
     */
    private function key(SpaceRef $space, IdentityRef $identity): string
    {
        return $space->externalId.'|'.$identity->externalId;
    }
}
