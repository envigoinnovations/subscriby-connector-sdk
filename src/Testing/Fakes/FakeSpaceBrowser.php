<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\SpaceBrowser;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\SpaceSummary;

/**
 * A browser that lists whatever places a test seeded, two sample places of any kind when none were.
 */
final class FakeSpaceBrowser implements SpaceBrowser
{
    /** @var array<string, list<SpaceSummary>> Seeded places keyed by kind. */
    public array $places = [];

    /** @var list<array{installation: string, kind: string}> Every browse call, oldest first. */
    public array $browsed = [];

    /**
     * @param   InstallationRef     $installation  The installation.
     * @param   CredentialBag       $credentials   Its secrets.
     * @param   string              $kind          The kind wanted.
     * @return  list<SpaceSummary>  The seeded places of that kind, or two samples.
     */
    public function browse(InstallationRef $installation, CredentialBag $credentials, string $kind): array
    {
        $this->browsed[] = ['installation' => $installation->id, 'kind' => $kind];

        return $this->places[$kind] ?? [
            new SpaceSummary('fake-'.$kind.'-1', $kind, ucfirst($kind).' one'),
            new SpaceSummary('fake-'.$kind.'-2', $kind, ucfirst($kind).' two'),
        ];
    }
}
