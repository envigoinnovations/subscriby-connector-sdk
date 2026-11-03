<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use DateTimeImmutable;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Events\AccessWindowChanged;

/**
 * The dated windows of access on an installation's places, for a connector that shows them on its platform ahead of time.
 *
 * A pass window is the core's: the ledger grants and revokes at its edges.
 * The core announces each window as it is scheduled, opened, closed and
 * cancelled through `AccessWindowChanged`, which is enough for a connector
 * that was listening; this read is for the windows it was not, the ones
 * scheduled before the connector was installed or beyond the horizon it
 * mirrors, so a daily sweep can catch up without the core knowing what
 * the connector makes of a window.
 */
interface AccessWindows
{
    /**
     * The windows on the installation's places that have not ended, soonest first, up to a horizon.
     *
     * @param   InstallationRef            $installation  The installation whose project's windows are meant.
     * @param   DateTimeImmutable          $until         The horizon: a window starting after it is left out.
     * @return  list<AccessWindowChanged>  Each window once per gated place of its plan on the installation's connector, with where it stands now; empty for an installation the core does not know.
     */
    public function listUpcoming(InstallationRef $installation, DateTimeImmutable $until): array;
}
