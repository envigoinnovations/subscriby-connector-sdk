<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A commitment kind a partner may still add, with the most it earns and where it is done.
 */
final readonly class PartnerCommitmentOption
{
    /**
     * @param  string                      $kind           The kind's value, the one a draft names.
     * @param  string                      $label          The kind in words.
     * @param  int                         $points         The most it adds, on the largest audience.
     * @param  string                      $channelsLabel  The channels it is done on, in words ("TikTok, Instagram, YouTube").
     * @param  list<PartnerChannelOption>  $channels       The same channels one by one, for the question that asks where a new audience lives.
     */
    public function __construct(
        public string $kind,
        public string $label,
        public int $points,
        public string $channelsLabel,
        public array $channels,
    ) {}
}
