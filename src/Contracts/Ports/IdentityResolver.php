<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\IdentitySummary;
use Subscriby\Connector\Data\InboundEnvelope;
use Subscriby\Connector\Data\InstallationRef;

/**
 * Who an inbound event is from, and what the platform knows about an account.
 *
 * Required of every connector. The core keeps the identity rows and decides
 * which creator or member an account belongs to; the connector only says which
 * account the platform is talking about.
 */
interface IdentityResolver
{
    /**
     * @param   InboundEnvelope       $envelope  The decoded event.
     * @return  IdentitySummary|null  The account that acted, or null for an event with no actor.
     */
    public function resolveInbound(InboundEnvelope $envelope): ?IdentitySummary;

    /**
     * @param   InstallationRef  $installation  The installation to ask through.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   string           $externalId    The platform's id for the account.
     * @return  IdentitySummary  What the platform says about it.
     */
    public function describe(InstallationRef $installation, CredentialBag $credentials, string $externalId): IdentitySummary;
}
