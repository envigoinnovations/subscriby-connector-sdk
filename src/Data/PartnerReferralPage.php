<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One page of a partner's referrals, newest first, paged by the core the way the dashboard pages them.
 */
final readonly class PartnerReferralPage
{
    /**
     * @param  list<PartnerReferralRecord>  $items     The referrals on this page.
     * @param  int                          $page      The page shown, from one.
     * @param  int                          $lastPage  The last page there is, at least one.
     * @param  int                          $total     How many referrals there are in all.
     */
    public function __construct(
        public array $items,
        public int $page,
        public int $lastPage,
        public int $total,
    ) {}
}
