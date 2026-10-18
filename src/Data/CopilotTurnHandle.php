<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * Where a connector keeps a turn's display between the core's calls, opaque to the core.
 *
 * A platform with streamed drafts keeps a draft id, one without keeps the
 * id of the message it edits, and a connector that had to fall back from
 * the first to the second says so here; the core carries the handle across
 * the queue and hands it back unread, which is why it holds scalars only.
 */
final readonly class CopilotTurnHandle
{
    /**
     * @param  array<string, scalar|null>  $meta  Whatever the connector needs to find the display again.
     */
    public function __construct(
        public array $meta = [],
    ) {}

    /**
     * @param   string  $key  A key the connector wrote.
     * @return  mixed   Its value, or null when the handle has none.
     */
    public function get(string $key): mixed
    {
        return $this->meta[$key] ?? null;
    }

    /**
     * A handle holding these values and more.
     *
     * @param   array<string, scalar|null>  $values  What to add or replace.
     * @return  self                        A new handle; this one is unchanged.
     */
    public function with(array $values): self
    {
        return new self(array_replace($this->meta, $values));
    }
}
