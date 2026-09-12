<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Data\RecoveryCoverage;

/**
 * What the application already keeps ready for a project's recovery, as a connector may read it.
 *
 * The readiness checklist is the connector's to word (a "standby bot", a
 * "mirrored channel") but the facts behind it are the application's: which
 * installation stands by, which spaces have a standby and whether it is
 * healthy, whether posts are mirrored, whether failover is automatic. A
 * connector reads them here and answers `RecoverySupport::readinessChecks()`
 * without importing an application class.
 */
interface Recovery
{
    /**
     * @param   ProjectRef        $project    The project.
     * @param   string            $connector  The connector whose installations and spaces are meant.
     * @return  RecoveryCoverage  The project's standing, one entry per space the connector gates.
     */
    public function coverage(ProjectRef $project, string $connector): RecoveryCoverage;
}
