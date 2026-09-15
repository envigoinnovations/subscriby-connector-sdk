<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Data\RecoveryCoverage;
use Subscriby\Connector\Data\ResourceRef;
use Subscriby\Connector\Data\SpaceRef;
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
}
