<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use DateTimeImmutable;
use Illuminate\Http\Request;
use Subscriby\Connector\Contracts\Ports\InboundGateway;
use Subscriby\Connector\Data\InboundEnvelope;
use Symfony\Component\HttpFoundation\Response;

/**
 * An inbound gateway that accepts a JSON body signed with a fixed header.
 *
 * A request is authentic when `X-Fake-Token` is `fake`; the body's `id` is the
 * idempotency key and its `kind` the event kind, so replays and routing can be
 * tested by posting JSON.
 */
final class FakeInboundGateway implements InboundGateway
{
    /** The header value a fake call must carry. */
    public const TOKEN = 'fake';

    /**
     * @param  string  $connector  The connector key the envelopes name.
     */
    public function __construct(
        private readonly string $connector,
    ) {}

    /**
     * @param   Request  $request  The call.
     * @return  bool     True when the header carries the fixed token.
     */
    public function authenticate(Request $request): bool
    {
        return $request->header('X-Fake-Token') === self::TOKEN;
    }

    /**
     * @param   Request                    $request  The call.
     * @return  iterable<InboundEnvelope>  One envelope from the JSON body.
     */
    public function decode(Request $request): iterable
    {
        $payload = (array) $request->json()->all();

        yield new InboundEnvelope(
            connector: $this->connector,
            installation: null,
            idempotencyKey: (string) ($payload['id'] ?? uniqid('fake-', true)),
            kind: (string) ($payload['kind'] ?? 'message'),
            payload: $payload,
            receivedAt: new DateTimeImmutable,
        );
    }

    /**
     * @param   Request        $request  The call.
     * @return  Response|null  Null: the core's default answer is enough.
     */
    public function immediateResponse(Request $request): ?Response
    {
        return null;
    }

    /**
     * @param   InboundEnvelope  $envelope  The event.
     * @return  bool             False: the fake handles everything inline.
     */
    public function shouldDefer(InboundEnvelope $envelope): bool
    {
        return false;
    }
}
