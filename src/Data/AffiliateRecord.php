<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\AffiliateStatus;

/**
 * One affiliate as the project's creator reads them: who they are, where they stand, what they brought in and what they are owed.
 *
 * The member's own summary ({@see AffiliateSummary}) never carries their
 * name or their payout details; the creator's does, because the creator
 * approves them by name and pays them where they asked to be paid. The
 * payout details are the affiliate's own words, decrypted for this read
 * alone, and a connector shows them to the creator and to nobody else.
 */
final readonly class AffiliateRecord
{
    /**
     * @param  string                      $id             The affiliate row's UUID.
     * @param  string                      $projectId      The project.
     * @param  string                      $memberId       The member's row.
     * @param  string                      $memberName     The member as the creator knows them.
     * @param  string                      $code           The code their friends type and their links carry.
     * @param  AffiliateStatus             $status         Where they stand.
     * @param  DateTimeImmutable|null      $joinedAt       When they joined.
     * @param  DateTimeImmutable|null      $approvedAt     When the creator approved them; null while pending.
     * @param  int                         $referred       Friends they referred so far.
     * @param  int                         $converted      Friends who paid.
     * @param  int                         $daysEarned     Membership days their referrals earned them, banked or applied.
     * @param  bool                        $paysCash       Whether the programme pays them money rather than days.
     * @param  list<AffiliateBalanceLine>  $balances       What they are owed per currency; empty on a free-days programme or before any commission.
     * @param  string|null                 $payoutDetails  Where they asked to be paid, in their own words, or null while they left nothing.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public string $memberId,
        public string $memberName,
        public string $code,
        public AffiliateStatus $status,
        public ?DateTimeImmutable $joinedAt,
        public ?DateTimeImmutable $approvedAt,
        public int $referred,
        public int $converted,
        public int $daysEarned,
        public bool $paysCash,
        public array $balances,
        public ?string $payoutDetails,
    ) {}

    /**
     * @return  list<AffiliateBalanceLine>  The currencies with something payable right now.
     */
    public function payableBalances(): array
    {
        return array_values(array_filter($this->balances, static fn (AffiliateBalanceLine $line): bool => $line->hasPayable()));
    }
}
