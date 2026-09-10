<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\InboundEnvelope;
use Subscriby\Connector\Enums\ManagementCommand;

/**
 * The creator's in-chat admin surface, rendered in the connector's idiom.
 *
 * Bound by connectors that declare `management_surface`. The core publishes
 * the command catalogue and the actions behind it; the connector declares the
 * subset it renders and handles the events that drive its screens (Telegram
 * wizards over chat storage, Discord slash commands and modals). The
 * directory shows the coverage.
 */
interface ManagementSurface
{
    /**
     * @return  list<ManagementCommand>  The commands this surface renders; must equal the manifest's list.
     */
    public function commands(): array;

    /**
     * Drive the surface with an event the inbound gateway decoded.
     *
     * @param  InboundEnvelope  $envelope  The event.
     */
    public function handle(InboundEnvelope $envelope): void;
}
