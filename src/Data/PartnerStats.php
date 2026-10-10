<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * An approved partner's tallies, the figures the dashboard's overview tiles show.
 *
 * Every count is the core's, read the way the dashboard reads them, so a
 * connector prints what the creator would see on the web.
 */
final readonly class PartnerStats
{
    /**
     * @param  int  $clicks       Visits the partner link brought.
     * @param  int  $referred     Creators who signed up through the partner, in any standing.
     * @param  int  $open         Of those, the ones still inside their conversion window.
     * @param  int  $converted    The ones who paid their first Starter or Growth invoice.
     * @param  int  $underReview  The ones held for the owner's review.
     */
    public function __construct(
        public int $clicks,
        public int $referred,
        public int $open,
        public int $converted,
        public int $underReview,
    ) {}
}
