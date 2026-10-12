<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The row the core wrote for a member's answer on the cancellation screen.
 *
 * A connector parks the id so the offer the member takes or passes on is
 * stamped on the same row, and reads the offer the core decided to show
 * next, which may differ from a screen drawn a while ago.
 */
final readonly class CancellationRecord
{
    /**
     * @param  string                      $id     The row's UUID.
     * @param  RetentionOfferSummary|null  $offer  The offer to show next, or null when the project makes none on this rail.
     */
    public function __construct(
        public string $id,
        public ?RetentionOfferSummary $offer,
    ) {}
}
