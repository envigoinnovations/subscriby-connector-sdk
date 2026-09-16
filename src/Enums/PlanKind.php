<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * The shapes of plan a place of a resource kind can be sold under.
 *
 * A manifest names them per resource kind (`plan_kinds`), so a connector
 * whose places cannot be left by force says so honestly: a place the
 * connector cannot revoke access to sells one-time purchases only, a place
 * that cannot hold a member at the door sells no passes. The core's own plan
 * enum is finer (a one-time purchase is a subscription that does not recur);
 * these four are the words a creator reads on a connector's card.
 */
enum PlanKind: string
{
    use EnumHelpers;

    /**
     * One payment, access that never renews.
     */
    case OneTime = 'one_time';

    /**
     * Access that runs on a billing cycle and ends when the member stops paying.
     */
    case Recurring = 'recurring';

    /**
     * A ticket to one dated access window.
     */
    case Pass = 'pass';

    /**
     * One payment for a curated slate of other plans' access windows.
     */
    case PassSeries = 'pass_series';
}
