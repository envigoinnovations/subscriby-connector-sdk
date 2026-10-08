<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\AffiliateStatus;

/**
 * A member's standing in a project's Referral Program, as a connector may show it to them.
 *
 * Their code, where they stand, and what their referrals have brought in;
 * the addresses they share come from `Core\Referrals::links()`, because a
 * connector builds its own deep link and the core only knows it once asked.
 */
final readonly class AffiliateSummary
{
    /**
     * @param  string                   $id           The affiliate row's UUID.
     * @param  string                   $projectId    The project whose programme they joined.
     * @param  string                   $memberId     The member row's UUID.
     * @param  string                   $code         The code their links carry and a friend may type.
     * @param  AffiliateStatus          $status       Where they stand.
     * @param  bool                     $programLive  Whether the programme attributes and rewards right now.
     * @param  bool                     $paysCash     Whether the programme pays money rather than membership days.
     * @param  int                      $referred     How many friends arrived through their links or code, paid or not.
     * @param  int                      $converted    How many of them made a first payment.
     * @param  int                      $daysEarned   Membership days their referrals earned them, banked or waiting.
     * @param  list<AffiliateEarnings>  $earnings     What they are owed and were paid, one entry per currency anything was earned in.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public string $memberId,
        public string $code,
        public AffiliateStatus $status,
        public bool $programLive,
        public bool $paysCash,
        public int $referred,
        public int $converted,
        public int $daysEarned,
        public array $earnings,
    ) {}
}
