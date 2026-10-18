<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * Where a creator stands with the Copilot, for the line a connector prints before the first question.
 */
final readonly class CopilotStanding
{
    /**
     * @param  bool    $allowed        Whether the creator's role includes the Copilot at all.
     * @param  int     $freeTurnsLeft  The free turns left in the creator's monthly allowance.
     * @param  string  $credit         The Copilot Credits balance, formatted in the core's words.
     * @param  bool    $exhausted      True when the allowance and the credit are both spent, so the next turn would be refused.
     */
    public function __construct(
        public bool $allowed,
        public int $freeTurnsLeft,
        public string $credit,
        public bool $exhausted,
    ) {}
}
