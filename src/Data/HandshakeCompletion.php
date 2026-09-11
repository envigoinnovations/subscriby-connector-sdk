<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What the core wrote when a connector completed a handshake.
 *
 * The connector only needs enough to tell the person what happened: which
 * handshake it was and whose account the proof was for, so its reply can
 * name them. The link itself is the core's; the connector never sees it.
 */
final readonly class HandshakeCompletion
{
    /**
     * @param  HandshakeRef  $handshake    The handshake that was completed.
     * @param  string        $subjectName  The display name of the person the account now belongs to.
     */
    public function __construct(
        public HandshakeRef $handshake,
        public string $subjectName,
    ) {}
}
