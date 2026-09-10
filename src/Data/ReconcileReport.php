<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a reconciliation sweep did to one installation's grants.
 *
 * Reconciliation re-asserts every live grant and clears every revoked one, so
 * a platform that does not replay missed events (Discord after a gateway
 * outage) and a platform whose grant silently failed (a Telegram link never
 * minted) both end in the state the ledger says.
 */
final readonly class ReconcileReport
{
    /**
     * @param  int           $checked     Grants examined.
     * @param  int           $reasserted  Grants the platform had lost and were given again.
     * @param  int           $revoked     Grants the platform still held and were taken away.
     * @param  list<string>  $failures    One line per grant that could not be brought into line.
     */
    public function __construct(
        public int $checked,
        public int $reasserted,
        public int $revoked,
        public array $failures = [],
    ) {}
}
