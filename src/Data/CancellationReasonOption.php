<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One reason a cancelling member may pick, as a connector lists it.
 *
 * The code is what the core records and the webhook carries; the label is
 * the core's own wording in the member's language, so a bot's buttons and
 * the portal's radio list say the same thing.
 */
final readonly class CancellationReasonOption
{
    /**
     * @param  string  $code   The stable code (`too_expensive`, `other`, …), short enough to ride in callback data.
     * @param  string  $label  The reason as the member reads it.
     */
    public function __construct(
        public string $code,
        public string $label,
    ) {}
}
