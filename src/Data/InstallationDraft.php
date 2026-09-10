<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * An installation that has begun but is not yet connected.
 *
 * A paste-a-token connector completes in one step and returns an empty draft;
 * an OAuth connector returns the URL the creator must visit and the state it
 * needs back when they return; a connector that needs more answers returns the
 * fields to ask next.
 */
final readonly class InstallationDraft
{
    /**
     * @param  array<string, mixed>  $state        What the connector needs to pick the flow up again.
     * @param  list<Field>           $fields       Further fields to ask the creator, if any.
     * @param  string|null           $continueUrl  Where the creator must go to continue, if anywhere.
     */
    public function __construct(
        public array $state = [],
        public array $fields = [],
        public ?string $continueUrl = null,
    ) {}

    /**
     * @return  bool  True when nothing more is needed from the creator.
     */
    public function isComplete(): bool
    {
        return $this->fields === [] && $this->continueUrl === null;
    }
}
