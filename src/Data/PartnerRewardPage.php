<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One page of a partner's commissions and clawbacks, newest first.
 */
final readonly class PartnerRewardPage
{
    /**
     * @param  list<PartnerRewardRecord>  $items     The rows on this page.
     * @param  int                        $page      The page shown, from one.
     * @param  int                        $lastPage  The last page there is, at least one.
     * @param  int                        $total     How many rows there are in all.
     */
    public function __construct(
        public array $items,
        public int $page,
        public int $lastPage,
        public int $total,
    ) {}
}
