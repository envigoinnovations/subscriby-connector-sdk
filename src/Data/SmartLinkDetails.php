<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * One Smart Link as a creator reads it on a connector: its funnel in the
 * core's figures and words, and the addresses to share.
 *
 * Every label is already in the creator's language and every amount already
 * formatted, so a connector prints lines and never composes a state, a
 * destination or a sum of its own.
 */
final readonly class SmartLinkDetails
{
    /**
     * @param  string                  $id             The link.
     * @param  string                  $projectId      The project it belongs to, for the way back.
     * @param  string                  $projectName    The project's name.
     * @param  string                  $name           What the creator called the link.
     * @param  string                  $slug           The address segment after `/go/`.
     * @param  string                  $code           The stable code in the bot link and the cookie.
     * @param  string                  $destination    Where it sends people, in words ("Portal home", "Plan: Premium", "Bot").
     * @param  string                  $state          Live, expiring on a date, expired or archived, in words.
     * @param  bool                    $live           Whether the link still counts and credits.
     * @param  string                  $webUrl         The `/go/` address.
     * @param  string|null             $botUrl         The bot's deep link, or null when the project runs no bot with a start link.
     * @param  string                  $embedUrl       The plan-cards page the creator frames on their site.
     * @param  int                     $clicks         Lifetime clicks.
     * @param  int                     $leads          Members the link brought.
     * @param  int                     $checkouts      Those members who started at least one purchase.
     * @param  int                     $paid           Those members who made a successful payment.
     * @param  string                  $conversion     Paid members per hundred clicks, formatted with its percent sign.
     * @param  string                  $revenueUsd     Everything those members ever paid, restated in US dollars and formatted.
     * @param  list<string>            $revenue        The same revenue per currency, each amount formatted with its currency; empty when nothing was paid.
     * @param  DateTimeImmutable|null  $lastClickedAt  When the link was last followed, or null when never.
     * @param  DateTimeImmutable       $createdAt      When the link was made.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public string $projectName,
        public string $name,
        public string $slug,
        public string $code,
        public string $destination,
        public string $state,
        public bool $live,
        public string $webUrl,
        public ?string $botUrl,
        public string $embedUrl,
        public int $clicks,
        public int $leads,
        public int $checkouts,
        public int $paid,
        public string $conversion,
        public string $revenueUsd,
        public array $revenue,
        public ?DateTimeImmutable $lastClickedAt,
        public DateTimeImmutable $createdAt,
    ) {}
}
