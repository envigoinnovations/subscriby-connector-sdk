<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\BackfillOptions;
use Subscriby\Connector\Data\BackfillReport;
use Subscriby\Connector\Data\VerificationReport;

/**
 * Moving a connector's legacy data into the neutral tables, and proving it moved.
 *
 * Optional, and not a capability: it exists for the connectors whose data
 * predates the neutral tables, which is the first connector and nobody
 * else. The core runs every bound migrator from `subscriby:connectors:backfill`
 * and `subscriby:connectors:verify-backfill`, so the platform-specific reads
 * of the legacy columns live in the connector package and the core commands
 * know only the report shapes. Both methods must be idempotent and safe to
 * run while the application serves traffic.
 */
interface DataMigrator
{
    /**
     * Write the neutral rows the legacy data implies, or report what would be written.
     *
     * @param   BackfillOptions  $options  Dry run and chunking.
     * @return  BackfillReport   Counts per table and every anomaly class with samples.
     */
    public function backfill(BackfillOptions $options): BackfillReport;

    /**
     * Compare the neutral tables with the legacy data they were filled from.
     *
     * @return  VerificationReport  Every assertion and whether it held.
     */
    public function verify(): VerificationReport;
}
