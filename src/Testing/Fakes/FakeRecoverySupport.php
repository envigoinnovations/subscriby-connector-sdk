<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\RecoverySupport;
use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\DeliveryFailure;
use Subscriby\Connector\Data\DeliveryResult;
use Subscriby\Connector\Data\FailOverReport;
use Subscriby\Connector\Data\HandshakeRef;
use Subscriby\Connector\Data\HealthReasonText;
use Subscriby\Connector\Data\IdentityRef;
use Subscriby\Connector\Data\InstallationHealth;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\InstallationSummary;
use Subscriby\Connector\Data\PortalLoginStart;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Data\ReadinessItem;
use Subscriby\Connector\Data\RecoveryVocabulary;
use Subscriby\Connector\Data\SpaceAccess;
use Subscriby\Connector\Data\SpaceRef;
use Subscriby\Connector\Enums\DeliveryFailureKind;
use Subscriby\Connector\Exceptions\UnsupportedByConnector;

/**
 * Recovery support that probes and nothing else, as a connector whose platform cannot ban a bot.
 *
 * Every facet beyond probes throws, which is exactly what a real connector
 * with `probes: true` alone does; the core must never reach them.
 */
final class FakeRecoverySupport implements RecoverySupport
{
    /**
     * @param  string  $connector  The connector key, for the exceptions.
     */
    public function __construct(
        private readonly string $connector,
    ) {}

    /**
     * @return  RecoveryVocabulary  Nouns unlike Telegram's.
     */
    public function vocabulary(): RecoveryVocabulary
    {
        return new RecoveryVocabulary('bot', 'room', 'fake account', 'membership');
    }

    /**
     * @param   string            $code  The reason code.
     * @return  HealthReasonText  Words that name the code, so a test can see the connector's text won over the core's fallback.
     */
    public function healthReasonText(string $code): HealthReasonText
    {
        return new HealthReasonText("Fake connector: {$code}", "The fake connector explains {$code}.");
    }

    /**
     * @param   InstallationRef      $installation  The installation.
     * @param   ProjectRef           $project       The project.
     * @return  list<ReadinessItem>  Nothing: the fake has nothing to prepare.
     */
    public function readinessChecks(InstallationRef $installation, ProjectRef $project): array
    {
        return [
            new ReadinessItem('fake_safeguard', 'A fake safeguard', 'Proves the checklist renders a line a connector worded.', 'sparkles', true, true, null, 'In place'),
        ];
    }

    /**
     * @param   InstallationRef     $installation  The installation.
     * @param   CredentialBag       $credentials   Its secrets.
     * @return  InstallationHealth  Always healthy.
     */
    public function probeInstallation(InstallationRef $installation, CredentialBag $credentials): InstallationHealth
    {
        return InstallationHealth::healthy();
    }

    /**
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   SpaceRef         $space         The place.
     * @return  SpaceAccess      Ready, unless the id says the place is lost.
     */
    public function probeSpace(InstallationRef $installation, CredentialBag $credentials, SpaceRef $space): SpaceAccess
    {
        return (new FakeSpaceCatalog)->diagnose($installation, $credentials, $space);
    }

    /**
     * @param   InstallationRef       $installation  The installation.
     * @param   CredentialBag         $credentials   Its secrets.
     * @param   IdentityRef           $identity      The account.
     * @return  DeliveryFailure|null  Gone for a `deleted-` identity, unreachable for a `blocked-` one, null otherwise.
     */
    public function probeIdentity(InstallationRef $installation, CredentialBag $credentials, IdentityRef $identity): ?DeliveryFailure
    {
        if (str_starts_with($identity->externalId, 'deleted-')) {
            return new DeliveryFailure(DeliveryFailureKind::TargetMissing, 'The account was deleted.', null, 'account_deleted');
        }

        if (str_starts_with($identity->externalId, 'blocked-')) {
            return new DeliveryFailure(DeliveryFailureKind::Unreachable, 'The account blocked the fake bot.');
        }

        return null;
    }

    /**
     * @param   ProjectRef           $project      The project.
     * @param   CredentialBag        $credentials  The standby's secrets.
     * @return  InstallationSummary  Never.
     *
     * @throws  UnsupportedByConnector  Always: the fake declares no standby installations.
     */
    public function registerStandbyInstallation(ProjectRef $project, CredentialBag $credentials): InstallationSummary
    {
        throw UnsupportedByConnector::facet($this->connector, 'standby installations');
    }

    /**
     * @param  InstallationRef  $standby      The standby.
     * @param  CredentialBag    $credentials  Its secrets.
     *
     * @throws  UnsupportedByConnector  Always: the fake declares no standby installations.
     */
    public function removeStandbyInstallation(InstallationRef $standby, CredentialBag $credentials): void
    {
        throw UnsupportedByConnector::facet($this->connector, 'standby installations');
    }

    /**
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   SpaceRef         $from          The lost place.
     * @param   SpaceRef         $to            The standby.
     * @param   iterable         $holders       Everyone to re-admit.
     * @return  FailOverReport   Never.
     *
     * @throws  UnsupportedByConnector  Always: the fake declares no resource standby.
     */
    public function failOver(InstallationRef $installation, CredentialBag $credentials, SpaceRef $from, SpaceRef $to, iterable $holders): FailOverReport
    {
        throw UnsupportedByConnector::facet($this->connector, 'resource standby');
    }

    /**
     * @param   InstallationRef  $installation    The installation.
     * @param   CredentialBag    $credentials     Its secrets.
     * @param   SpaceRef         $from            The mirrored place.
     * @param   SpaceRef         $to              The standby.
     * @param   string           $externalPostId  The post.
     * @return  DeliveryResult   Never.
     *
     * @throws  UnsupportedByConnector  Always: the fake declares no mirror.
     */
    public function mirror(InstallationRef $installation, CredentialBag $credentials, SpaceRef $from, SpaceRef $to, string $externalPostId): DeliveryResult
    {
        throw UnsupportedByConnector::facet($this->connector, 'mirroring');
    }

    /**
     * @param   InstallationRef   $platform   The platform installation.
     * @param   CreatorRef        $creator    The creator.
     * @param   HandshakeRef      $handshake  The handshake.
     * @return  PortalLoginStart  Never.
     *
     * @throws  UnsupportedByConnector  Always: the fake declares no identity relink.
     */
    public function beginIdentityHandshake(InstallationRef $platform, CreatorRef $creator, HandshakeRef $handshake): PortalLoginStart
    {
        throw UnsupportedByConnector::facet($this->connector, 'identity relink');
    }
}
