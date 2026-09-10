<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What the core hands a slot contribution to render with.
 *
 * Refs only, never Eloquent models: a slot view reads its context and the
 * connector's own services, and knows nothing about the core's tables.
 */
final readonly class SlotContext
{
    /**
     * @param  InstallationRef|null  $installation  The installation the slot is rendered for, when the page has one.
     * @param  ProjectRef|null       $project       The project, when the page has one.
     * @param  array<string, mixed>  $refs          Further refs the slot needs, keyed by name (`resource`, `space`, `grant`, `member`).
     */
    public function __construct(
        public ?InstallationRef $installation = null,
        public ?ProjectRef $project = null,
        public array $refs = [],
    ) {}
}
