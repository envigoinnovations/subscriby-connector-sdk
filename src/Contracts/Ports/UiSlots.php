<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\SlotContribution;

/**
 * The connector's contributions to the slots of the core UI.
 *
 * Required of every connector, even one that fills nothing: returning an
 * empty list is how a connector says the core's generic rendering is enough.
 * A contribution reads only its `SlotContext` and the connector's own services
 * and ships a placeholder, so skeleton parity holds for connector UI too.
 */
interface UiSlots
{
    /**
     * @return  list<SlotContribution>  One contribution per slot the connector fills.
     */
    public function slots(): array;
}
