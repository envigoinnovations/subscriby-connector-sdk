<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One Smart Link on a creator's list, with the three figures a glance needs.
 *
 * The clicks, the leads and the paid members are lifetime counts, the same
 * the dashboard's table shows; the rest of the funnel is on the link's own
 * screen.
 */
final readonly class SmartLinkSummary
{
    /**
     * @param  string  $id      The link.
     * @param  string  $name    What the creator called it.
     * @param  string  $slug    The address segment after `/go/`.
     * @param  bool    $live    Whether the link still counts and credits; false once archived or expired.
     * @param  int     $clicks  Lifetime clicks.
     * @param  int     $leads   Members the link brought.
     * @param  int     $paid    Those members who made a successful payment.
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public bool $live,
        public int $clicks,
        public int $leads,
        public int $paid,
    ) {}
}
