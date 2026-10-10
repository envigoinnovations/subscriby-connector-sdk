<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What an add-a-commitment conversation asks before its first question.
 *
 * The kinds the partner may still add, each with the channels it is done
 * on; the audience bands that earn anything, so a conversation offers only
 * what the core would accept (the floor and everything under it are left
 * out, because the core refuses a commitment on such an audience); the
 * floor in words, for the sentence that explains the list; and how many
 * days the proof has from the day a commitment is added.
 */
final readonly class PartnerCommitmentOptions
{
    /**
     * @param  list<PartnerCommitmentOption>  $kinds       The kinds the partner holds in no state yet; empty once every kind is held.
     * @param  list<PartnerReachOption>       $reachBands  The audience bands above the reach floor, smallest first.
     * @param  string                         $reachFloor  The smallest band that earns anything, in words.
     * @param  int                            $proofDays   The days a commitment added today has to send its proof.
     */
    public function __construct(
        public array $kinds,
        public array $reachBands,
        public string $reachFloor,
        public int $proofDays,
    ) {}

    /**
     * @param   string                        $value  A kind's value as a button sent it back.
     * @return  PartnerCommitmentOption|null  The kind among those the partner may add, or null.
     */
    public function kind(string $value): ?PartnerCommitmentOption
    {
        foreach ($this->kinds as $kind) {
            if ($kind->kind === $value) {
                return $kind;
            }
        }

        return null;
    }
}
