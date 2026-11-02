<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Where a dated access window stands, as the core tells a connector about it.
 *
 * A pass sells a window of time on a place; a connector that mirrors the
 * window on its platform (a scheduled event in a server) follows these four
 * words and never the core's pass engine.
 */
enum AccessWindowState: string
{
    use EnumHelpers;

    case Scheduled = 'scheduled';
    case Open = 'open';
    case Closed = 'closed';
    case Cancelled = 'cancelled';

    /**
     * @return  bool  True while the window is still to come or running, so a mirror of it should exist.
     */
    public function isLive(): bool
    {
        return $this === self::Scheduled || $this === self::Open;
    }
}
