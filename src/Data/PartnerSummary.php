<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\PartnerAttention;
use Subscriby\Connector\Enums\PartnerStanding;

/**
 * A creator's standing in the Partner Program, as a connector's creator surface shows it.
 *
 * The words are the core's ({@see $pitch}, {@see $termsLines}, {@see $rateSentence}),
 * so a connector prints the programme the way the dashboard does and never
 * composes a rate from points. What is set depends on the standing: a
 * creator who never applied reads the pitch and the terms alone; an
 * applicant their application date; a rejected one the reason and when
 * they may apply again; an approved one the code, the links, the rate,
 * the tallies, the balance, how they are paid and what still stands in
 * the way of being paid.
 */
final readonly class PartnerSummary
{
    /**
     * @param  PartnerStanding             $standing            Where the creator stands.
     * @param  string                      $pitch               The programme in one sentence.
     * @param  string                      $rateLabel           The rate's range in words ("from 5% up to 30% of every invoice").
     * @param  list<string>                $termsLines          The programme's terms as a partner is told them, one sentence a line.
     * @param  string|null                 $code                The partner's code; null until approved.
     * @param  list<PartnerLink>           $links               The addresses the partner shares, labelled; empty until approved.
     * @param  string|null                 $shareText           A ready sentence with the web link, for a share button; null until approved.
     * @param  int|null                    $ratePercent         The current rate, whole percent; null until approved.
     * @param  string|null                 $rateSentence        The rate broken down (base, commitments, volume); null until approved.
     * @param  string|null                 $nextTierSentence    The next volume tier, or that every tier is reached; null until approved.
     * @param  PartnerStats|null           $stats               The tallies; null until approved.
     * @param  PartnerBalanceSummary|null  $balance             The balance; null until approved.
     * @param  string|null                 $payoutMethodLabel   How the partner chose to be paid, in words; null until chosen.
     * @param  string|null                 $payoutDestination   Where the chosen rail pays, in one line; null until given.
     * @param  bool                        $termsAccepted       Whether the Partner Program Terms were accepted.
     * @param  DateTimeImmutable|null      $termsAcceptedAt     When; null until accepted.
     * @param  PartnerAttention|null       $attention           What stands in the way of being paid, or null when nothing does.
     * @param  DateTimeImmutable|null      $appliedAt           When the application was filed; null for a creator who never applied.
     * @param  string|null                 $rejectionReason     Why the owner turned the application down; null unless rejected.
     * @param  DateTimeImmutable|null      $reapplyAvailableAt  When a rejected creator may apply again; null unless rejected.
     */
    public function __construct(
        public PartnerStanding $standing,
        public string $pitch,
        public string $rateLabel,
        public array $termsLines,
        public ?string $code = null,
        public array $links = [],
        public ?string $shareText = null,
        public ?int $ratePercent = null,
        public ?string $rateSentence = null,
        public ?string $nextTierSentence = null,
        public ?PartnerStats $stats = null,
        public ?PartnerBalanceSummary $balance = null,
        public ?string $payoutMethodLabel = null,
        public ?string $payoutDestination = null,
        public bool $termsAccepted = false,
        public ?DateTimeImmutable $termsAcceptedAt = null,
        public ?PartnerAttention $attention = null,
        public ?DateTimeImmutable $appliedAt = null,
        public ?string $rejectionReason = null,
        public ?DateTimeImmutable $reapplyAvailableAt = null,
    ) {}

    /**
     * @return  string|null  The web link, the one every approved partner has, or null before approval.
     */
    public function webLink(): ?string
    {
        foreach ($this->links as $link) {
            if ($link->label === 'Web') {
                return $link->url;
            }
        }

        return $this->links[0]->url ?? null;
    }
}
