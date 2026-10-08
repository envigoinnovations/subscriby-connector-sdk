<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One page of a project's affiliates, as the creator's surface lists them.
 *
 * A chat keyboard holds a handful of rows, so the creator's list is paged
 * by the core the way the dashboard's is, and a connector asks for the
 * next page by number rather than holding the whole roll.
 */
final readonly class AffiliatePage
{
    /**
     * @param  list<AffiliateRecord>  $items     The affiliates on this page.
     * @param  int                    $page      The page shown, from one.
     * @param  int                    $lastPage  The last page there is, at least one.
     * @param  int                    $total     How many affiliates match in all.
     */
    public function __construct(
        public array $items,
        public int $page,
        public int $lastPage,
        public int $total,
    ) {}
}
