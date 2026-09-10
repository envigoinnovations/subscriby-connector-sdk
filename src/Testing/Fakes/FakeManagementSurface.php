<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\ManagementSurface;
use Subscriby\Connector\Data\InboundEnvelope;
use Subscriby\Connector\Enums\ManagementCommand;

/**
 * A management surface that renders a declared subset of the catalogue and records what it handled.
 */
final class FakeManagementSurface implements ManagementSurface
{
    /** @var list<InboundEnvelope> */
    public array $handled = [];

    /**
     * @param  list<ManagementCommand>  $commands  The commands the fake surface renders.
     */
    public function __construct(
        private readonly array $commands,
    ) {}

    /**
     * @return  list<ManagementCommand>  The declared subset.
     */
    public function commands(): array
    {
        return $this->commands;
    }

    /**
     * @param  InboundEnvelope  $envelope  The event.
     */
    public function handle(InboundEnvelope $envelope): void
    {
        $this->handled[] = $envelope;
    }
}
