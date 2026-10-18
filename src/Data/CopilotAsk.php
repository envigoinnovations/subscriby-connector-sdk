<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A creator's message to the Copilot, with where it came from and the thread it continues.
 *
 * The connector parks the thread the last ticket named and hands it back
 * here; a thread the core no longer holds is replaced by a new one without
 * a refusal, because a pruned thread is nothing the creator did wrong. A
 * question the creator tapped (a follow-up button) rather than typed is
 * marked so the answer can quote it, since the chat never showed it.
 */
final readonly class CopilotAsk
{
    /**
     * @param  InstallationRef  $installation  The installation the creator is talking through.
     * @param  IdentityRecord   $account       The account that is talking.
     * @param  string           $message       What they said, as typed or as the button read.
     * @param  string|null      $threadId      The thread to continue, or null to open one.
     * @param  string|null      $projectId     The project the conversation is about, when it was opened from one.
     * @param  bool             $newThread     True to leave the given thread and open a fresh one.
     * @param  bool             $showQuestion  True when the message came from a button, so the answer repeats it.
     */
    public function __construct(
        public InstallationRef $installation,
        public IdentityRecord $account,
        public string $message,
        public ?string $threadId = null,
        public ?string $projectId = null,
        public bool $newThread = false,
        public bool $showQuestion = false,
    ) {}
}
