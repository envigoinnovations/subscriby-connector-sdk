<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\HandshakeRef;
use Subscriby\Connector\Data\IdentityRecord;
use Subscriby\Connector\Data\IncidentSummary;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\MirrorPair;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Data\RecoveryCoverage;
use Subscriby\Connector\Data\RelinkDetection;
use Subscriby\Connector\Data\ResourceRef;
use Subscriby\Connector\Data\SpaceRef;
use Subscriby\Connector\Exceptions\HandshakeRefused;
use Subscriby\Connector\Exceptions\RecoveryRefused;
use Subscriby\Connector\Exceptions\ResourceRefused;

/**
 * What the application keeps ready for a project's recovery, as a connector may read it, and the two places it may file.
 *
 * The readiness checklist is the connector's to word (a "standby bot", a
 * "mirrored channel") but the facts behind it are the application's: which
 * installation stands by, which spaces have a standby and whether it is
 * healthy, whether posts are mirrored, whether failover is automatic. A
 * connector reads them here and answers `RecoverySupport::readinessChecks()`
 * without importing an application class. The two writes are the answers to
 * a `Standby` and a `Replacement` link request: the creator picked a place on
 * the platform, and the connector files it here so the core's owner guard,
 * tier gate, ledger, allowance and re-admission run exactly as they do from
 * the dashboard.
 */
interface Recovery
{
    /**
     * @param   ProjectRef        $project    The project.
     * @param   string            $connector  The connector whose installations and spaces are meant.
     * @return  RecoveryCoverage  The project's standing, one entry per space the connector gates.
     */
    public function coverage(ProjectRef $project, string $connector): RecoveryCoverage;

    /**
     * File a place as a resource's standby, the reserve the core switches to when the resource's place is lost.
     *
     * @param  ResourceRef  $resource  The resource the standby is for.
     * @param  SpaceRef     $standby   The place to keep in reserve, as the connector names it.
     * @param  bool         $mirror    Whether posts in the resource's place are copied into it from now on.
     *
     * @throws  ResourceRefused  When no resource has that id.
     * @throws  RecoveryRefused  When the core refuses: the actor does not own the project, the plan lacks prevention, the project switched standby places off, or the place is another kind, already sold or already a standby.
     */
    public function registerStandby(ResourceRef $resource, SpaceRef $standby, bool $mirror = false): void;

    /**
     * Point a resource at the place that replaces the one it lost, re-admitting everyone with access.
     *
     * @param  ResourceRef  $resource     The resource whose place is being replaced.
     * @param  SpaceRef     $replacement  The place taking over, as the connector names it.
     *
     * @throws  ResourceRefused  When no resource has that id.
     * @throws  RecoveryRefused  When the core refuses: the actor does not own the project, the place is another kind or already sold, or the self-service allowance is spent.
     */
    public function replaceSpace(ResourceRef $resource, SpaceRef $replacement): void;

    /**
     * Complete a recovery handshake with the account that answered it: the creator proved a new account, or a backup one.
     *
     * The token is the one the core minted for the wizard and the
     * connector's start link carried; the account is recorded as what was
     * detected and the dashboard confirms the change afterwards, so the
     * connector tells the person only what was detected and where to go.
     *
     * @param   string           $token     The token as the start link carried it, the recovery prefix stripped.
     * @param   IdentityRecord   $identity  The account that answered, as the connector describes it.
     * @return  RelinkDetection  What was detected: a replacement or a backup, and the account's name.
     *
     * @throws  HandshakeRefused  `expired` when the token names no pending handshake, `identity_held` when another creator holds the account, `same_identity` when it is the creator's own.
     */
    public function completeRelink(string $token, IdentityRecord $identity): RelinkDetection;

    /**
     * The places the core mirrors for an installation, each with the standby its posts are copied into.
     *
     * The core reads no connector's posts, so a connector that hears a post
     * asks here whether the place is mirrored and copies it through its own
     * recovery port; the answer is empty for a standby whose mirror switch is
     * off, whose place the last probe found unhealthy, or whose project
     * switched the mirror facet off for the connector, so a flipped switch
     * stops the copies at once.
     *
     * @param   InstallationRef   $installation  The installation whose project's places are meant.
     * @return  list<MirrorPair>  Each mirrored resource with its place and its standby; empty for an installation the core does not know.
     */
    public function mirrorPairs(InstallationRef $installation): array;

    /**
     * Note that a post was just copied into a resource's standby, for the prevention page's "last mirrored" line.
     *
     * @param  ResourceRef  $resource  The resource whose standby received the copy.
     *
     * @throws  ResourceRefused  When no resource has that id.
     */
    public function recordMirrored(ResourceRef $resource): void;

    /**
     * Open a recovery handshake for a creator: a replacement for the account they sign in with, or a backup one.
     *
     * The dashboard's wizards open the same handshake; a creator surface
     * opens it from the platform and hands the creator the start link its
     * `RecoverySupport::beginIdentityHandshake()` builds, to open from the
     * other account. The core's own gates apply: the creator must be the
     * account holder, the platform installation must keep the relink facet
     * on, a replacement spends the account allowance and a backup needs the
     * prevention tier.
     *
     * @param   CreatorRef    $creator  Who is proving a new account.
     * @param   bool          $backup   True to register a backup account rather than replace the creator's own.
     * @return  HandshakeRef  The open handshake, its purpose `RecoveryRelink` or `BackupIdentity`.
     *
     * @throws  RecoveryRefused  When the core refuses, with its reason and the sentence for the creator.
     * @throws  ResourceRefused  When no creator has that id.
     */
    public function openRelink(CreatorRef $creator, bool $backup = false): HandshakeRef;

    /**
     * A creator's open incidents, newest first, for a status read.
     *
     * @param   CreatorRef             $creator  The creator.
     * @return  list<IncidentSummary>  What the core detected and has not seen resolved.
     *
     * @throws  ResourceRefused  When no creator has that id.
     */
    public function incidentsFor(CreatorRef $creator): array;

    /**
     * `registerStandby()` as the creator a surface names, for a surface answering from a job where nobody is bound.
     *
     * @param  CreatorRef   $creator   Who is linking.
     * @param  ResourceRef  $resource  The resource the standby is for.
     * @param  SpaceRef     $standby   The place to keep in reserve.
     * @param  bool         $mirror    Whether posts in the resource's place are copied into it.
     *
     * @throws  ResourceRefused  When no resource or no creator has that id.
     * @throws  RecoveryRefused  When the application refuses the standby.
     */
    public function registerStandbyAs(CreatorRef $creator, ResourceRef $resource, SpaceRef $standby, bool $mirror = false): void;

    /**
     * `replaceSpace()` as the creator a surface names.
     *
     * @param  CreatorRef   $creator      Who is swapping.
     * @param  ResourceRef  $resource     The resource whose place is being replaced.
     * @param  SpaceRef     $replacement  The place taking over.
     *
     * @throws  ResourceRefused  When no resource or no creator has that id.
     * @throws  RecoveryRefused  When the application refuses the swap.
     */
    public function replaceSpaceAs(CreatorRef $creator, ResourceRef $resource, SpaceRef $replacement): void;
}
