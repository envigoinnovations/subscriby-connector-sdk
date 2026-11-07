<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Where a holder stands on one date of the Pass Roster, in the core's six words.
 *
 * A creator scanning a date needs one word per holder: whether they are in,
 * waiting in the join queue, still to tap their link, missed the date, lost
 * it, or hold it through another purchase that is the one admitting them.
 * The core reads the word from its ledger; a surface only prints it, with
 * the translated word the core hands over beside it.
 */
enum PassHolderStanding: string
{
    use EnumHelpers;

    case In = 'in';
    case Queued = 'queued';
    case Holds = 'holds';
    case Missed = 'missed';
    case Ended = 'ended';
    case Covered = 'covered';

    /**
     * @return  bool  True while the holder is expected through the door: admitted, queued, or still to join.
     */
    public function isExpected(): bool
    {
        return $this === self::In || $this === self::Queued || $this === self::Holds;
    }
}
