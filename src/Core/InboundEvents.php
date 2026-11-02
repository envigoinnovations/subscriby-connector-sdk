<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\InboundEnvelope;

/**
 * The core's record of which inbound events it has already seen, for a connector that receives events outside HTTP.
 *
 * A webhook connector never calls this: the core's inbound middleware
 * records every event a request carries before the connector's handler
 * runs. A gateway connector holds a socket open and builds the envelopes
 * itself, so it records each one here before queueing it, and a replay
 * after a resumed session is dropped exactly as a retried webhook is.
 */
interface InboundEvents
{
    /**
     * @param   InboundEnvelope  $envelope  The decoded event, with its stable idempotency key.
     * @return  bool             True the first time the key is seen for the connector; false for a replay.
     */
    public function accept(InboundEnvelope $envelope): bool;
}
