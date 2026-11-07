<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\PassMoveReason;

/**
 * One hop a pass made from a date to another, as the core's move log keeps it.
 */
final readonly class PassHop
{
    /**
     * @param  string                  $id           The hop's UUID.
     * @param  string                  $fromDateId   The date the pass left.
     * @param  string                  $fromLabel    That date's day label, as the dashboard prints it.
     * @param  string|null             $toDateId     The date it landed on, or null when the pass was ended instead.
     * @param  string|null             $toLabel      That date's day label, or null.
     * @param  PassMoveReason          $reason       Why it moved.
     * @param  string                  $reasonWord   The dashboard's translated label for the reason.
     * @param  string|null             $movedByName  Who made the hop, or null when the core did on its own.
     * @param  DateTimeImmutable|null  $movedAt      When.
     */
    public function __construct(
        public string $id,
        public string $fromDateId,
        public string $fromLabel,
        public ?string $toDateId,
        public ?string $toLabel,
        public PassMoveReason $reason,
        public string $reasonWord,
        public ?string $movedByName,
        public ?DateTimeImmutable $movedAt,
    ) {}
}
