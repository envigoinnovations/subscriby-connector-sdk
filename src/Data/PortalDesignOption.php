<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One choice a portal design control offers: the value a save sends and the label a screen prints.
 */
final readonly class PortalDesignOption
{
    /**
     * @param  string  $value  The stored value.
     * @param  string  $label  The name in the creator's language.
     */
    public function __construct(
        public string $value,
        public string $label,
    ) {}
}
