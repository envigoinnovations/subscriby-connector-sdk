<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A creator's answer to a confirmation card the Copilot paused on.
 *
 * The card is named by its id alone: the core finds the thread behind it
 * and checks the creator owns it, so a connector's button carries nothing
 * but the id and a stale tap on an old card is refused rather than acted on.
 */
final readonly class CopilotDecision
{
    /**
     * @param  InstallationRef  $installation  The installation the creator is talking through.
     * @param  IdentityRecord   $account       The account that is deciding.
     * @param  string           $actionId      The card.
     * @param  bool             $approve       True to run the held tool, false to decline it.
     */
    public function __construct(
        public InstallationRef $installation,
        public IdentityRecord $account,
        public string $actionId,
        public bool $approve,
    ) {}
}
