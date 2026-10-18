<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CopilotAsk;
use Subscriby\Connector\Data\CopilotDecision;
use Subscriby\Connector\Data\CopilotStanding;
use Subscriby\Connector\Data\CopilotTurnTicket;
use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Exceptions\CopilotRefused;

/**
 * Subscriby Copilot as a connector's creator surface reaches it: the operator
 * over the creator's own tools, answering in the chat the creator is already in.
 *
 * A turn never runs inside the connector's request. The core checks that the
 * creator may use the Copilot and can pay for a turn, opens the connector's
 * own thinking display through its `CopilotSurface` port, and runs the model
 * on the queue, streaming what it does into that display and sending the
 * answer when it is done. The connector shows nothing itself beyond parking
 * the thread it was handed: the words, the buttons and the confirmation card
 * all come from the core, in the creator's language, so a connector never
 * composes an answer or decides what may run.
 */
interface Copilot
{
    /**
     * Where the creator stands with the Copilot: whether they may use it and what a turn draws on.
     *
     * @param   CreatorRef       $creator  The creator asking.
     * @return  CopilotStanding  Whether their role allows it, the free turns left this month, the credit, and whether both are spent.
     *
     * @throws  CopilotRefused  `creator_unknown` when the ref names no creator.
     */
    public function standing(CreatorRef $creator): CopilotStanding;

    /**
     * Send a message to the operator: the thinking display opens at once and the turn runs on the queue.
     *
     * @param   CreatorRef         $creator  The creator talking.
     * @param   CopilotAsk         $ask      The message, the installation and account it came through, and the thread to continue.
     * @return  CopilotTurnTicket  The thread the turn belongs to, a new one when none was given or the given one is gone.
     *
     * @throws  CopilotRefused  `creator_unknown`, `not_allowed`, `credit_exhausted`, `cap_reached`, `message_invalid`, `project_unknown` or `surface_unavailable`.
     */
    public function ask(CreatorRef $creator, CopilotAsk $ask): CopilotTurnTicket;

    /**
     * Answer a confirmation card: confirm runs the held tool on the queue like a turn, cancel settles it at once.
     *
     * @param   CreatorRef              $creator   The creator deciding.
     * @param   CopilotDecision         $decision  The card and the answer.
     * @return  CopilotTurnTicket|null  The thread the continuation runs in, or null for a cancel, which runs nothing and costs nothing.
     *
     * @throws  CopilotRefused  `creator_unknown`, `not_allowed`, `action_unknown`, `action_expired`, `credit_exhausted`, `cap_reached` or `surface_unavailable`.
     */
    public function decide(CreatorRef $creator, CopilotDecision $decision): ?CopilotTurnTicket;
}
