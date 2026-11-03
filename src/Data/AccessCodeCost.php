<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a batch of access codes costs the creator before it is queued.
 *
 * The creator's plan includes some codes a month; a batch beyond them is
 * billed, and a surface shows the cost and asks before queueing, because
 * the charge cannot be undone once the codes exist.
 */
final readonly class AccessCodeCost
{
    /**
     * @param  int     $freeRemaining       The codes still included this month before the batch.
     * @param  int     $chargeableQuantity  How many of the batch are billed.
     * @param  string  $cost                The charge in words, with its currency, `0` when nothing is billed.
     * @param  bool    $exceedsThreshold    Whether the charge is large enough that the core asks for an explicit confirmation.
     */
    public function __construct(
        public int $freeRemaining,
        public int $chargeableQuantity,
        public string $cost,
        public bool $exceedsThreshold,
    ) {}

    /**
     * @return  bool  True when the batch is billed at all.
     */
    public function billed(): bool
    {
        return $this->chargeableQuantity > 0;
    }
}
