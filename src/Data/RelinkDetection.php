<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The account a creator proved for a recovery, as the core detected it.
 *
 * The dashboard's wizard confirms the change afterwards; the connector only
 * tells the person what was detected and where to go next.
 */
final readonly class RelinkDetection
{
    /**
     * @param  bool    $backup       True when the handshake registers a backup account, false when it replaces the creator's identity.
     * @param  string  $displayName  The name the platform reported for the detected account.
     */
    public function __construct(
        public bool $backup,
        public string $displayName,
    ) {}
}
