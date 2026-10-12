<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * What a connector shows a member who asked to cancel, before the confirmation it always had.
 *
 * The reason question comes first when the project asks it; the offer comes
 * next when the project makes one on the member's rail; a connector that
 * reads neither goes straight to its own confirmation. Both are decided by
 * the core from the project's settings, the owner's plan and the rail, so a
 * connector never reads a setting itself.
 */
final readonly class CancellationScreen
{
    /**
     * @param  string                          $subscriptionId  The live membership the screen is about.
     * @param  string                          $projectName     The project's name, for the connector's own lines.
     * @param  string                          $planName        The plan's name, for the connector's own lines.
     * @param  bool                            $asksReason      Whether the project wants to know why.
     * @param  list<CancellationReasonOption>  $reasons         The reasons to pick from, in order; empty when the project asks none.
     * @param  RetentionOfferSummary|null      $offer           The offer the project makes on this rail, or null.
     * @param  string|null                     $accessEndsOn    The day access ends after a cancellation, formatted, or null when the membership has no end yet.
     */
    public function __construct(
        public string $subscriptionId,
        public string $projectName,
        public string $planName,
        public bool $asksReason,
        public array $reasons,
        public ?RetentionOfferSummary $offer,
        public ?string $accessEndsOn,
    ) {}
}
