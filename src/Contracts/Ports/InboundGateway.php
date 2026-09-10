<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Illuminate\Http\Request;
use Subscriby\Connector\Data\InboundEnvelope;
use Symfony\Component\HttpFoundation\Response;

/**
 * Turning what the platform sends into events the core can route.
 *
 * Required of every connector. The core owns the route, the idempotency table
 * and the queue; the connector owns the platform's wire format and its
 * authenticity check. A gateway-based platform (Discord) has no HTTP request:
 * its worker builds envelopes itself and this port's request methods are
 * never called for it.
 */
interface InboundGateway
{
    /**
     * @param   Request  $request  The platform's call.
     * @return  bool     True when the call provably came from the platform.
     */
    public function authenticate(Request $request): bool;

    /**
     * @param   Request                    $request  The platform's call.
     * @return  iterable<InboundEnvelope>  The events it carried, each with a stable idempotency key.
     */
    public function decode(Request $request): iterable;

    /**
     * What to answer the platform before any event is handled.
     *
     * @param   Request        $request  The platform's call.
     * @return  Response|null  A response the platform needs at once, or null for the core's default `204`.
     */
    public function immediateResponse(Request $request): ?Response;

    /**
     * @param   InboundEnvelope  $envelope  A decoded event.
     * @return  bool             True when the event should be handled on a queue rather than inside the request.
     */
    public function shouldDefer(InboundEnvelope $envelope): bool;
}
