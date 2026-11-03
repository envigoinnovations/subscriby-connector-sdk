<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What the core answers when a batch of access codes is queued: the batch's key and the cost the creator agreed to.
 *
 * The codes themselves reach the creator on their account once generated,
 * so a surface tells them to watch for the file or the messages.
 */
final readonly class AccessCodeReceipt
{
    /**
     * @param  string          $batchKey  The key the core generates the batch under, unique per plan and request.
     * @param  AccessCodeCost  $cost      The cost as previewed at the moment of queueing.
     */
    public function __construct(
        public string $batchKey,
        public AccessCodeCost $cost,
    ) {}
}
