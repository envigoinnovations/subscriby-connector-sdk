<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One page of a partner's recorded payouts, newest first.
 */
final readonly class PartnerPayoutPage
{
    /**
     * @param  list<PartnerPayoutRecord>  $items     The payouts on this page.
     * @param  int                        $page      The page shown, from one.
     * @param  int                        $lastPage  The last page there is, at least one.
     * @param  int                        $total     How many payouts there are in all.
     */
    public function __construct(
        public array $items,
        public int $page,
        public int $lastPage,
        public int $total,
    ) {}
}
