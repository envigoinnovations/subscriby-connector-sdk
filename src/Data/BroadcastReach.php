<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * How far a broadcast reaches: the members it will be sent to and how long the send takes.
 *
 * A surface shows both before the creator confirms, because a broadcast
 * cannot be recalled once queued and every recipient is a person.
 */
final readonly class BroadcastReach
{
    /**
     * @param  int     $count         How many members receive it.
     * @param  int     $etaSeconds    How long the send takes at the platform's pace.
     * @param  string  $etaFormatted  The same in words, as the dashboard prints it.
     */
    public function __construct(
        public int $count,
        public int $etaSeconds,
        public string $etaFormatted,
    ) {}
}
