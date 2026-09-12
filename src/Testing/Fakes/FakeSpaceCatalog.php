<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\SpaceCatalog;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\IdentityRef;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\LinkRequest;
use Subscriby\Connector\Data\SpaceAccess;
use Subscriby\Connector\Data\SpaceRef;
use Subscriby\Connector\Data\SpaceSummary;
use Subscriby\Connector\Enums\LinkPurpose;

/**
 * A space catalogue that parks link requests in memory and reports a place lost when its id says so.
 *
 * One request per creator and purpose, as the contract promises; a space
 * whose external id starts with `lost-` diagnoses as not a member, so the
 * core's health and failover paths can be driven without a platform.
 */
final class FakeSpaceCatalog implements SpaceCatalog
{
    /** @var array<string, LinkRequest> Open requests keyed `creator|purpose`. */
    public array $linkRequests = [];

    /**
     * @param  InstallationRef  $installation  The installation.
     * @param  CredentialBag    $credentials   Its secrets.
     * @param  IdentityRef      $creator       Who is asked.
     * @param  LinkRequest      $request       What for.
     */
    public function requestLink(InstallationRef $installation, CredentialBag $credentials, IdentityRef $creator, LinkRequest $request): void
    {
        $this->linkRequests[$this->key($creator, $request->purpose)] = $request;
    }

    /**
     * @param  InstallationRef  $installation  The installation.
     * @param  CredentialBag    $credentials   Its secrets.
     * @param  IdentityRef      $creator       Whose request.
     * @param  LinkPurpose      $purpose       Which request.
     */
    public function withdrawLinkRequest(InstallationRef $installation, CredentialBag $credentials, IdentityRef $creator, LinkPurpose $purpose): void
    {
        unset($this->linkRequests[$this->key($creator, $purpose)]);
    }

    /**
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   IdentityRef      $creator       Whose request.
     * @param   LinkPurpose      $purpose       Which request.
     * @return  string|null      The subject of the open request, or null.
     */
    public function pendingLinkRequest(InstallationRef $installation, CredentialBag $credentials, IdentityRef $creator, LinkPurpose $purpose): ?string
    {
        return $this->linkRequests[$this->key($creator, $purpose)]->subjectId ?? null;
    }

    /**
     * @param   LinkPurpose  $purpose  Which request.
     * @return  string       A sentence unlike any platform's.
     */
    public function linkInstructions(LinkPurpose $purpose): string
    {
        return sprintf('Pick the room for the %s request in the fake console.', $purpose->value);
    }

    /**
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   SpaceRef         $space         The place.
     * @return  SpaceSummary     A title derived from the id.
     */
    public function describe(InstallationRef $installation, CredentialBag $credentials, SpaceRef $space): SpaceSummary
    {
        return new SpaceSummary($space->externalId, $space->kind, 'Room '.$space->externalId, $space->parentExternalId);
    }

    /**
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   SpaceRef         $space         The place.
     * @return  SpaceAccess      Not a member for a `lost-` id, ready otherwise.
     */
    public function diagnose(InstallationRef $installation, CredentialBag $credentials, SpaceRef $space): SpaceAccess
    {
        if (str_starts_with($space->externalId, 'lost-')) {
            return new SpaceAccess(false, SpaceAccess::NOT_MEMBER, 'The fake bot is no longer in this room.', true);
        }

        return SpaceAccess::ready();
    }

    /**
     * @param   IdentityRef  $creator  The creator's account.
     * @param   LinkPurpose  $purpose  The purpose.
     * @return  string       The request key.
     */
    private function key(IdentityRef $creator, LinkPurpose $purpose): string
    {
        return $creator->externalId.'|'.$purpose->value;
    }
}
