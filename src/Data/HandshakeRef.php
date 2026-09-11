<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\HandshakePurpose;

/**
 * An open two-sided proof that a person controls an account on a connector.
 *
 * The core mints the token and shows or sends it; the connector recognises it
 * when the account presents it (a `/start` payload, a typed code) and reports
 * the identity back; the core completes the handshake. Two sides have to agree,
 * which is what stops a leaked token binding a stranger's account.
 */
final readonly class HandshakeRef
{
    /**
     * @param  string            $id       The handshake row's UUID.
     * @param  HandshakePurpose  $purpose  What completing it will do.
     * @param  string            $token    The one-time token the account must present.
     */
    public function __construct(
        public string $id,
        public HandshakePurpose $purpose,
        public string $token,
    ) {}

    /**
     * @return  string  The deep-link payload a connector routes on: the purpose's prefix followed by the token.
     */
    public function startPayload(): string
    {
        return $this->purpose->startPayloadPrefix().$this->token;
    }
}
