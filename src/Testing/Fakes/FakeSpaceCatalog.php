<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\SpaceCatalog;
use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\SpaceAccess;
use Subscriby\Connector\Data\SpaceRef;
use Subscriby\Connector\Data\SpaceSummary;
use Subscriby\Connector\Enums\LinkPurpose;

/**
 * A space catalogue that records link requests and reports a place lost when its id says so.
 *
 * A space whose external id starts with `lost-` diagnoses as not a member, so
 * the core's health and failover paths can be driven without a platform.
 */
final class FakeSpaceCatalog implements SpaceCatalog
{
    /** @var list<array{creator: string, kind: string, purpose: LinkPurpose}> */
    public array $linkRequests = [];

    /**
     * @param  InstallationRef  $installation  The installation.
     * @param  CredentialBag    $credentials   Its secrets.
     * @param  CreatorRef       $creator       Who is asked.
     * @param  string           $kind          The kind wanted.
     * @param  LinkPurpose      $purpose       What for.
     */
    public function requestLink(InstallationRef $installation, CredentialBag $credentials, CreatorRef $creator, string $kind, LinkPurpose $purpose): void
    {
        $this->linkRequests[] = ['creator' => $creator->id, 'kind' => $kind, 'purpose' => $purpose];
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
}
