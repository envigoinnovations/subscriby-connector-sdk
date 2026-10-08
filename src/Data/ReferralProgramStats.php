<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The figures a creator reads on a Referral Program's overview, counted by the core.
 *
 * The same six tiles the dashboard shows: how many affiliates stand where,
 * how many friends were referred and how many of them paid, the days the
 * programme granted, and what is payable now per currency, already formatted.
 */
final readonly class ReferralProgramStats
{
    /**
     * @param  int           $approved     Affiliates whose code attributes.
     * @param  int           $pending      Affiliates awaiting the creator's approval.
     * @param  int           $suspended    Affiliates whose code no longer attributes.
     * @param  int           $referred     Friends referred so far, whatever became of them.
     * @param  int           $open         Referred friends still inside their attribution window, not yet paid.
     * @param  int           $converted    Referred friends who made their first payment.
     * @param  int           $daysGranted  Free membership days banked for referrers and friends.
     * @param  list<string>  $payable      Commissions past their hold and not yet paid out, one formatted amount with its currency per currency; empty when nothing is owed.
     */
    public function __construct(
        public int $approved,
        public int $pending,
        public int $suspended,
        public int $referred,
        public int $open,
        public int $converted,
        public int $daysGranted,
        public array $payable,
    ) {}

    /**
     * @return  int  How many members stand in the programme, whatever their standing.
     */
    public function affiliates(): int
    {
        return $this->approved + $this->pending + $this->suspended;
    }

    /**
     * @return  int  The share of referred friends who paid, as a whole percentage.
     */
    public function conversionRate(): int
    {
        return $this->referred === 0 ? 0 : (int) round($this->converted / $this->referred * 100);
    }
}
