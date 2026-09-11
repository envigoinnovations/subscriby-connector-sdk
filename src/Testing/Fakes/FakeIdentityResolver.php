<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\IdentityResolver;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\IdentityRecord;
use Subscriby\Connector\Data\IdentitySummary;
use Subscriby\Connector\Data\InboundEnvelope;
use Subscriby\Connector\Data\InstallationRef;

/**
 * Reads the actor out of a fake envelope's `from` key.
 *
 * External ids are phone-number shaped on purpose (`+15550001`), so a core
 * path that treats an external id as a numeric chat id fails here. Adopting
 * an account keeps no row: the storage ref is derived from the id, so a core
 * path that needs the connector's row to exist fails here too. Like the
 * first-party connectors, an adopted account is keyed on the account alone
 * (one account is one identity however many installations see it), so the
 * record names no installation.
 */
final class FakeIdentityResolver implements IdentityResolver
{
    /**
     * @param   InstallationRef  $installation  The installation the account is seen by; it fixes the connector, not the record's installation.
     * @param   IdentitySummary  $identity      The account as reported.
     * @return  IdentityRecord   The record, keyed on the account alone, its storage ref derived from the id.
     */
    public function adopt(InstallationRef $installation, IdentitySummary $identity): IdentityRecord
    {
        return new IdentityRecord(
            connector: $installation->connector,
            externalId: $identity->externalId,
            displayName: $identity->displayName,
            username: $identity->username,
            avatarUrl: $identity->avatarUrl,
            storageRef: 'fake-identity-'.$identity->externalId,
            meta: $identity->meta,
        );
    }

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
