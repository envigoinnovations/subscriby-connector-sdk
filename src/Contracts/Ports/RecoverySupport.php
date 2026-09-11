<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\DeliveryFailure;
use Subscriby\Connector\Data\DeliveryResult;
use Subscriby\Connector\Data\FailOverReport;
use Subscriby\Connector\Data\GrantSnapshot;
use Subscriby\Connector\Data\HandshakeRef;
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
use Subscriby\Connector\Exceptions\InstallationRefused;
use Subscriby\Connector\Exceptions\UnsupportedByConnector;

/**
 * What the Disaster Recovery Program needs a connector to witness and do.
 *
 * Bound by connectors that declare any `recovery_*` capability. The program
 * is one core subsystem with five facets a platform may or may not have; the
 * manifest's `recovery` block says which are real, the core never calls a
 * facet the manifest denies, and a connector implements the others by
 * throwing {@see UnsupportedByConnector}.
 */
interface RecoverySupport
{
    /**
     * @return  RecoveryVocabulary  The nouns the core's recovery pages use for this connector.
     */
    public function vocabulary(): RecoveryVocabulary;

    /**
     * @param   InstallationRef      $installation  The installation.
     * @param   ProjectRef           $project       The project the checklist is for.
     * @return  list<ReadinessItem>  The connector's lines of the readiness checklist.
     */
    public function readinessChecks(InstallationRef $installation, ProjectRef $project): array;

    /**
     * @param   InstallationRef     $installation  The installation to probe.
     * @param   CredentialBag       $credentials   Its secrets.
     * @return  InstallationHealth  What the platform said.
     */
    public function probeInstallation(InstallationRef $installation, CredentialBag $credentials): InstallationHealth;

    /**
     * @param   InstallationRef  $installation  The installation asking.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   SpaceRef         $space         The place to probe.
     * @return  SpaceAccess      Whether the place still exists and the installation still controls it.
     */
    public function probeSpace(InstallationRef $installation, CredentialBag $credentials, SpaceRef $space): SpaceAccess;

    /**
     * Ask the platform whether an account still exists and can be reached.
     *
     * Three answers, because the core acts differently on each: null when the
     * platform still reaches the account, `TargetMissing` when the account is
     * gone (the incident the recovery program exists for), any other kind when
     * the account exists but cannot be reached or the platform could not be
     * asked, which the core leaves alone.
     *
     * @param   InstallationRef       $installation  The installation asking.
     * @param   CredentialBag         $credentials   Its secrets.
     * @param   IdentityRef           $identity      The account to probe.
     * @return  DeliveryFailure|null  Null while the account answers, otherwise the classified failure.
     */
    public function probeIdentity(InstallationRef $installation, CredentialBag $credentials, IdentityRef $identity): ?DeliveryFailure;

    /**
     * Keep a second installation ready to take over from a project's live one.
     *
     * The standby is proven with the platform and stored, but registered for
     * nothing: it receives no events and answers nobody until a failover
     * writes it into the live installation's place. The core records the
     * neutral installation row from what comes back, so the connector keeps
     * only its own row and says where that is.
     *
     * @param   ProjectRef           $project      The project it stands by for.
     * @param   CredentialBag        $credentials  The standby's secrets, as the creator pasted them.
     * @return  InstallationSummary  What the platform says the standby is, its `storageRef` the connector's own row.
     *
     * @throws  UnsupportedByConnector  When the manifest declares no standby installations.
     * @throws  InstallationRefused     When another installation already holds the credentials, or the platform refuses them.
     */
    public function registerStandbyInstallation(ProjectRef $project, CredentialBag $credentials): InstallationSummary;

    /**
     * Forget a standby; it never acted on the platform, so there is nothing to withdraw there.
     *
     * @param  InstallationRef  $standby      The standby, its `storageRef` the connector's own row.
     * @param  CredentialBag    $credentials  Its secrets.
     *
     * @throws  UnsupportedByConnector  When the manifest declares no standby installations.
     */
    public function removeStandbyInstallation(InstallationRef $standby, CredentialBag $credentials): void;

    /**
     * Move every holder from a lost place to its standby.
     *
     * @param   InstallationRef          $installation  The installation acting.
     * @param   CredentialBag            $credentials   Its secrets.
     * @param   SpaceRef                 $from          The lost place.
     * @param   SpaceRef                 $to            The standby.
     * @param   iterable<GrantSnapshot>  $holders       Everyone who should be in the standby.
     * @return  FailOverReport           Who was re-admitted and who could not be.
     *
     * @throws  UnsupportedByConnector  When the manifest declares no resource standby.
     */
    public function failOver(InstallationRef $installation, CredentialBag $credentials, SpaceRef $from, SpaceRef $to, iterable $holders): FailOverReport;

    /**
     * Copy one post from a place into its standby.
     *
     * @param   InstallationRef  $installation    The installation acting.
     * @param   CredentialBag    $credentials     Its secrets.
     * @param   SpaceRef         $from            The mirrored place.
     * @param   SpaceRef         $to              The standby.
     * @param   string           $externalPostId  The platform's id for the post.
     * @return  DeliveryResult   Copied, or the classified failure.
     *
     * @throws  UnsupportedByConnector  When the manifest declares no mirror.
     */
    public function mirror(InstallationRef $installation, CredentialBag $credentials, SpaceRef $from, SpaceRef $to, string $externalPostId): DeliveryResult;

    /**
     * Start proving that a creator controls an account, for a relink or a backup identity.
     *
     * @param   InstallationRef   $platform   The platform-scope installation the account will talk to.
     * @param   CreatorRef        $creator    Who is proving it.
     * @param   HandshakeRef      $handshake  The open handshake carrying the token to present.
     * @return  PortalLoginStart  Where the creator goes, or what to do by hand.
     *
     * @throws  UnsupportedByConnector  When the manifest declares no identity relink.
     */
    public function beginIdentityHandshake(InstallationRef $platform, CreatorRef $creator, HandshakeRef $handshake): PortalLoginStart;
}
