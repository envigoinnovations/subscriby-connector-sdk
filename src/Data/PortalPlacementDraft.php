<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One part of the portal in its place, as a creator sends it.
 *
 * A connector resends the whole list in the order it wants, every part the
 * details listed exactly once; a part left out keeps its place at the end,
 * shown, and a key the details never listed is refused.
 */
final readonly class PortalPlacementDraft
{
    /**
     * @param  string  $key    The part, as the details gave it.
     * @param  bool    $shown  Whether the portal draws it.
     */
    public function __construct(
        public string $key,
        public bool $shown = true,
    ) {}
}
