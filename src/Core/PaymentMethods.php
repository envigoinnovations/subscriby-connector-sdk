<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\PaymentProviderKey;

/**
 * The payment-method rows, as a connector may read and write them.
 *
 * Only the migration half exists so far: before providers carried
 * `connector:provider` keys, the one native currency the platform knew was
 * stored under the platform-wide word `platformcurrency`, and the connector
 * that owns that currency rewrites those rows to its own key from its data
 * migrator. Listing and configuring methods stays with the dashboard and the
 * connector's own management surface until a port needs it.
 */
interface PaymentMethods
{
    /**
     * @return  int  Rows still stored under the platform-wide word rather than a connector's key.
     */
    public function countLegacyPlatformCurrencyRows(): int;

    /**
     * Rewrite every row stored under the platform-wide word into the connector's own provider key.
     *
     * Idempotent: rows already carrying a key are untouched, so the migrator
     * may run it on every deploy.
     *
     * @param   PaymentProviderKey  $key     The connector's native provider, `connector:provider`.
     * @param   bool                $dryRun  Report the rows without writing.
     * @return  int                 The rows rewritten, or that a real run would rewrite.
     */
    public function adoptLegacyPlatformCurrencyRows(PaymentProviderKey $key, bool $dryRun): int;
}
