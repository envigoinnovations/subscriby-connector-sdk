<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * One event the platform sent, as the connector decoded it.
 *
 * `idempotencyKey` is the connector's stable key for the event (Telegram's
 * `update_id` per bot, Discord's event id), which the core records before any
 * handler runs so a retried webhook or a replayed gateway batch is processed
 * once. `kind` is the connector's own vocabulary; the core routes on it only
 * where the SDK names the kind (a handshake code, a support message).
 */
final readonly class InboundEnvelope
{
    /**
     * @param  string                $connector       The connector key.
     * @param  InstallationRef|null  $installation    The installation the event arrived on, when known.
     * @param  string                $idempotencyKey  The connector's stable key for this event.
     * @param  string                $kind            The connector's kind of event.
     * @param  array<string, mixed>  $payload         The decoded event.
     * @param  DateTimeImmutable     $receivedAt      When it arrived here.
     */
    public function __construct(
        public string $connector,
        public ?InstallationRef $installation,
        public string $idempotencyKey,
        public string $kind,
        public array $payload,
        public DateTimeImmutable $receivedAt,
    ) {}
}
