<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * An installation that has begun but is not yet connected.
 *
 * A paste-a-token connector completes in one step and returns an empty draft;
 * an OAuth connector returns the URL the creator must visit and the state it
 * needs back when they return; a connector that needs more answers returns the
 * fields to ask next. The core parks an incomplete draft on the installation
 * row, sends the creator on or asks the fields, and hands the draft back to
 * `complete()` with what the platform returned, or to `begin()` again with
 * the state and the new answers.
 */
final readonly class InstallationDraft
{
    /**
     * @param  array<string, mixed>  $state        What the connector needs to pick the flow up again.
     * @param  list<Field>           $fields       Further fields to ask the creator, if any.
     * @param  string|null           $continueUrl  Where the creator must go to continue, if anywhere.
     * @param  array<string, mixed>  $returned     What the platform handed back when the creator returned from `continueUrl`: the query string of the return leg, as the core received it.
     */
    public function __construct(
        public array $state = [],
        public array $fields = [],
        public ?string $continueUrl = null,
        public array $returned = [],
    ) {}

    /**
     * @return  bool  True when nothing more is needed from the creator.
     */
    public function isComplete(): bool
    {
        return $this->fields === [] && $this->continueUrl === null;
    }

    /**
     * The same draft, carrying what the platform handed back on the return leg.
     *
     * @param   array<string, mixed>  $returned  The return leg's query string.
     * @return  self                  A draft `complete()` can finish from.
     */
    public function withReturned(array $returned): self
    {
        return new self($this->state, $this->fields, $this->continueUrl, $returned);
    }
}
