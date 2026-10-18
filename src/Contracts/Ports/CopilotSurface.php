<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\CopilotTurnAnswer;
use Subscriby\Connector\Data\CopilotTurnHandle;
use Subscriby\Connector\Data\CopilotTurnOpening;
use Subscriby\Connector\Data\CopilotTurnProgress;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\Recipient;

/**
 * Showing a Copilot turn to a creator inside the connector's chat, from the first thinking to the finished answer.
 *
 * Bound by connectors that declare `copilot`. The core drives the four calls
 * in order from the queue: `begin()` the moment a message is accepted, so the
 * creator sees the Copilot thinking before any model answers; `progress()`
 * every second or so while the model streams, carrying the whole state each
 * time so a connector repaints rather than appends; then one `finish()` or
 * one `fail()`. What a display is (a streamed draft on a platform that has
 * them, an edited message on one that does not) is the connector's own, kept
 * in the handle the core carries back unopened.
 */
interface CopilotSurface
{
    /**
     * Open the display a turn streams into.
     *
     * @param   InstallationRef     $installation  The installation the creator is talking through.
     * @param   CredentialBag       $credentials   Its secrets.
     * @param   Recipient           $recipient     The creator's account.
     * @param   CopilotTurnOpening  $opening       The thread, the thinking sentence, and the question to show when the creator tapped it rather than typed it.
     * @return  CopilotTurnHandle   What the connector needs to find the display again, opaque to the core.
     */
    public function begin(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, CopilotTurnOpening $opening): CopilotTurnHandle;

    /**
     * Repaint the display with what the turn has done so far.
     *
     * @param   InstallationRef      $installation  The installation the creator is talking through.
     * @param   CredentialBag        $credentials   Its secrets.
     * @param   Recipient            $recipient     The creator's account.
     * @param   CopilotTurnHandle    $handle        The display, as the last call answered.
     * @param   CopilotTurnProgress  $progress      The thinking so far, the tools reached for, and the answer so far.
     * @return  CopilotTurnHandle    The display, which may have moved when a draft gave way to a message.
     */
    public function progress(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, CopilotTurnHandle $handle, CopilotTurnProgress $progress): CopilotTurnHandle;

    /**
     * Replace the display with the finished answer and its buttons.
     *
     * @param  InstallationRef    $installation  The installation the creator is talking through.
     * @param  CredentialBag      $credentials   Its secrets.
     * @param  Recipient          $recipient     The creator's account.
     * @param  CopilotTurnHandle  $handle        The display, as the last call answered.
     * @param  CopilotTurnAnswer  $answer        The answer, where its work opens, the follow-ups and the card waiting on the creator.
     */
    public function finish(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, CopilotTurnHandle $handle, CopilotTurnAnswer $answer): void;

    /**
     * Replace the display with the one sentence that says why there is no answer.
     *
     * @param  InstallationRef    $installation  The installation the creator is talking through.
     * @param  CredentialBag      $credentials   Its secrets.
     * @param  Recipient          $recipient     The creator's account.
     * @param  CopilotTurnHandle  $handle        The display, as the last call answered.
     * @param  string             $sentence      The refusal or failure in the creator's language.
     */
    public function fail(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, CopilotTurnHandle $handle, string $sentence): void;
}
