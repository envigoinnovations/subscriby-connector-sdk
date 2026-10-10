<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a payout-details conversation needs to know before its first question.
 *
 * The rails with what each asks for, the minimum a cash payout runs from,
 * the settlement sentence the dashboard's dialog shows, and whether the
 * terms were accepted already, so a connector asks for them only once.
 */
final readonly class PartnerPayoutOptions
{
    /**
     * @param  list<PartnerRailOption>  $rails               The rails, in the core's order.
     * @param  string                   $minimumPayout       The cash minimum, formatted.
     * @param  string                   $settlementSentence  How and when payable commissions reach a partner, in the core's words.
     * @param  bool                     $termsAccepted       Whether the Partner Program Terms were accepted already.
     * @param  string                   $termsUrl            Where the public Terms live.
     */
    public function __construct(
        public array $rails,
        public string $minimumPayout,
        public string $settlementSentence,
        public bool $termsAccepted,
        public string $termsUrl,
    ) {}

    /**
     * @param   string                  $value  A rail's value.
     * @return  PartnerRailOption|null  The rail, or null when no rail carries that value.
     */
    public function rail(string $value): ?PartnerRailOption
    {
        foreach ($this->rails as $rail) {
            if ($rail->value === $value) {
                return $rail;
            }
        }

        return null;
    }
}
