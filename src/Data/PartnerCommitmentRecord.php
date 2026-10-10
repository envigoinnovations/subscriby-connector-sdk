<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One promotion commitment of a partner, as the dashboard's commitments card lists it.
 *
 * The points are the ones the row holds today (frozen at verification,
 * nothing before it), beside the most the kind can add, and the moment is
 * the core's sentence for the standing (proof due by a date, proof sent and
 * waiting, verified on a date, lapsed on a date).
 */
final readonly class PartnerCommitmentRecord
{
    /**
     * @param  string       $id             The commitment row's UUID.
     * @param  string       $kind           The kind's value, the one {@see PartnerCommitmentDraft} names.
     * @param  string       $kindLabel      The kind in words.
     * @param  string       $standingLabel  Promised, verified or lapsed, in words.
     * @param  int          $points         The points it adds to the rate today.
     * @param  int          $maxPoints      The most the kind can add, on the largest audience.
     * @param  string       $moment         What the standing means for this row, with its date.
     * @param  string|null  $audienceLabel  The audience it is done on (channel and address); null when none is linked.
     * @param  string|null  $reachLabel     That audience's reach band; null when none is linked.
     * @param  string|null  $proofUrl       Where the work can be seen, once proof was sent; null before.
     * @param  bool         $acceptsProof   Whether proof may be sent or replaced now (never once verified).
     */
    public function __construct(
        public string $id,
        public string $kind,
        public string $kindLabel,
        public string $standingLabel,
        public int $points,
        public int $maxPoints,
        public string $moment,
        public ?string $audienceLabel,
        public ?string $reachLabel,
        public ?string $proofUrl,
        public bool $acceptsProof,
    ) {}
}
