<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Why a pass left one date for another, as the core's move log keeps it.
 *
 * A cancelled date rolled a single-date holder forward, a cancelled date in
 * a season was substituted with another, or a payment settled after the
 * date it was for and the purchase was bound to the next one instead. A
 * surface prints the translated word the core hands over beside the case.
 */
enum PassMoveReason: string
{
    use EnumHelpers;

    case WindowCancelled = 'window_cancelled';
    case SeriesDateSubstituted = 'series_date_substituted';
    case PaidAfterWindow = 'paid_after_window';
}
