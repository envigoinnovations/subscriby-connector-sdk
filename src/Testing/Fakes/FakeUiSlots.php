<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\UiSlots;
use Subscriby\Connector\Data\SlotContribution;

/**
 * A connector that fills no slot, so every core page must render it generically.
 */
final class FakeUiSlots implements UiSlots
{
    /**
     * @return  list<SlotContribution>  Nothing.
     */
    public function slots(): array
    {
        return [];
    }
}
