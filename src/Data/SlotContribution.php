<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\ConnectorSlot;
use Subscriby\Connector\Exceptions\InvalidManifest;

/**
 * What a connector renders into one slot of the core UI.
 *
 * Either a Blade view in the connector's own namespace or a Livewire component
 * the connector registers, never both, plus the placeholder the core includes
 * while a lazy page loads, so skeleton parity holds for connector UI too.
 */
final readonly class SlotContribution
{
    /**
     * @param  ConnectorSlot  $slot         The slot being filled.
     * @param  string|null    $view         A Blade view name in the connector's namespace, for static UI.
     * @param  string|null    $component    A Livewire component name, for interactive UI.
     * @param  string|null    $placeholder  The Blade view the core includes while a lazy page loads.
     *
     * @throws  InvalidManifest  When neither or both of a view and a component are given.
     */
    public function __construct(
        public ConnectorSlot $slot,
        public ?string $view = null,
        public ?string $component = null,
        public ?string $placeholder = null,
    ) {
        if (($view === null) === ($component === null)) {
            throw InvalidManifest::because('unknown', sprintf('slot %s must name exactly one of a view or a component', $slot->value));
        }
    }
}
