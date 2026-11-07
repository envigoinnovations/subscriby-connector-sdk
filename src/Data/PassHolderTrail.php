<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One pass in full: its holder, the date it is on now, and every hop it made.
 */
final readonly class PassHolderTrail
{
    /**
     * @param  PassHolder      $holder  The holder, with their standing on the date.
     * @param  PassRosterDate  $date    The date the pass is on now, with its roll call.
     * @param  list<PassHop>   $hops    Every hop the pass made, oldest first; empty for a pass that never moved.
     */
    public function __construct(
        public PassHolder $holder,
        public PassRosterDate $date,
        public array $hops,
    ) {}
}
