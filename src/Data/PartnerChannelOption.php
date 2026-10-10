<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One place a commitment can be done on, offered when a partner describes a new audience.
 */
final readonly class PartnerChannelOption
{
    /**
     * @param  string  $value  The channel's value, the one a {@see PartnerCommitmentDraft} names.
     * @param  string  $label  The channel in words, in the creator's language.
     */
    public function __construct(
        public string $value,
        public string $label,
    ) {}
}
