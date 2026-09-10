<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\IdentityResolver;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\IdentitySummary;
use Subscriby\Connector\Data\InboundEnvelope;
use Subscriby\Connector\Data\InstallationRef;

/**
 * Reads the actor out of a fake envelope's `from` key.
 *
 * External ids are phone-number shaped on purpose (`+15550001`), so a core
 * path that treats an external id as a numeric chat id fails here.
 */
final class FakeIdentityResolver implements IdentityResolver
{
    /**
     * @param   InboundEnvelope       $envelope  The decoded event.
     * @return  IdentitySummary|null  The `from` account, or null when the event has none.
     */
    public function resolveInbound(InboundEnvelope $envelope): ?IdentitySummary
    {
        $from = $envelope->payload['from'] ?? null;

        if (! is_array($from) || ! isset($from['id'])) {
            return null;
        }

        return new IdentitySummary((string) $from['id'], (string) ($from['name'] ?? 'Someone'));
    }

    /**
     * @param   InstallationRef  $installation  The installation asking.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   string           $externalId    The account.
     * @return  IdentitySummary  A name derived from the id.
     */
    public function describe(InstallationRef $installation, CredentialBag $credentials, string $externalId): IdentitySummary
    {
        return new IdentitySummary($externalId, 'Member '.$externalId);
    }
}
