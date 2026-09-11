<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What the core answers when a connector completes an identity handshake.
 *
 * The connector replies to the account that answered: who they now are (the
 * creator they linked to, the member they signed in as) and, when the
 * handshake had a destination, where they go next.
 */
final readonly class HandshakeCompletion
{
    /**
     * @param  HandshakeRef  $handshake    The handshake, now consumed.
     * @param  string        $subjectName  The display name of the person the account now belongs to, for the reply.
     * @param  string|null   $returnUrl    Where the person goes next, when the handshake has a destination (a portal sign-in: the portal).
     */
    public function __construct(
        public HandshakeRef $handshake,
        public string $subjectName,
        public ?string $returnUrl = null,
    ) {}
}
