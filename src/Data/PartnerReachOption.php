<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One audience size a partner may declare for a new audience.
 */
final readonly class PartnerReachOption
{
    /**
     * @param  string  $value  The band's value, the one a {@see PartnerCommitmentDraft} names.
     * @param  string  $label  The band in words, in the creator's language ("10,001 – 50,000").
     */
    public function __construct(
        public string $value,
        public string $label,
    ) {}
}
