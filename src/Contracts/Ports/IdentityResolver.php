<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\IdentityRecord;
use Subscriby\Connector\Data\IdentitySummary;
use Subscriby\Connector\Data\InboundEnvelope;
use Subscriby\Connector\Data\InstallationRef;

/**
 * Who an inbound event is from, and what the platform knows about an account.
 *
 * Required of every connector. The core keeps the identity rows and decides
 * which creator or member an account belongs to; the connector only says which
 * account the platform is talking about, and keeps whatever row of its own it
 * needs to talk to that account later.
 */
interface IdentityResolver
{
    /**
     * Take in an account the platform vouched for outside an inbound event, and describe it as the neutral record.
     *
     * A sign-in widget or a completed handshake hands the core an account the
     * connector has never seen an update from. The connector keeps its own row
     * for it under the installation given (Telegram: the private chat row under
     * the platform bot), so the account can be written to, and answers with
     * the record the core files, its `storageRef` naming that row. Calling it
     * again for the same account returns the same row.
     *
     * @param   InstallationRef  $installation  The installation the account is seen by.
     * @param   IdentitySummary  $identity      The account as the platform reported it.
     * @return  IdentityRecord   The record the core files, with the connector's storage ref.
     */
    public function adopt(InstallationRef $installation, IdentitySummary $identity): IdentityRecord;

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
