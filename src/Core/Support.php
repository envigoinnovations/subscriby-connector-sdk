<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\ConversationRef;
use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\IdentityRecord;
use Subscriby\Connector\Data\InboundSupportMessage;
use Subscriby\Connector\Data\IngestedSupportMessage;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\SpaceRef;
use Subscriby\Connector\Data\SupportConversationSummary;
use Subscriby\Connector\Data\SupportMessageRef;
use Subscriby\Connector\Data\SupportRelayTarget;
use Subscriby\Connector\Enums\SupportReplySource;
use Subscriby\Connector\Exceptions\SupportRefused;

/**
 * The support inbox, as a connector feeds it and answers from it.
 *
 * The inbox is the core's: it opens one thread per member per connector,
 * keeps the messages, throttles a flood, drops a blocked member's words,
 * shows the thread in the dashboard and announces it to webhooks. A connector
 * is the way words enter and leave: it hands the core what a member wrote in
 * the installation's private conversation, sends back the project's
 * acknowledgement, and, when the creator answers on the platform rather than
 * in the dashboard (a reply typed into the relay's ping, or written inside the
 * thread the relay opened in a group), hands that answer to the core too, so
 * every reply is recorded, delivered and announced the same way whatever
 * surface it came from. The `SupportRelay` port is the other direction: the
 * core calling the connector to carry a reply or a thread to the platform.
 */
interface Support
{
    /**
     * Whether the project behind an installation takes support messages at all.
     *
     * Asked before anything is recorded, so a connector can hand a message
     * the project does not want to its own fallback ("I did not understand
     * that") instead of filing it.
     *
     * @param   InstallationRef  $installation  The installation the message arrived through.
     * @return  bool             True when the project's support inbox is switched on; false for a project that turned it off, or an installation that names no project.
     */
    public function acceptsMessages(InstallationRef $installation): bool;

    /**
     * File a member's message into the project's inbox.
     *
     * The core records the account (idempotent on the platform's own id),
     * finds the member who holds it in the installation's project or creates
     * them as a lead, opens or reopens their thread on this connector, and
     * records the message; a message the platform already delivered is
     * updated in place by its id, so an edit and a replayed webhook both
     * leave one row. The answer carries the project's acknowledgement when
     * one is due, for the connector to send.
     *
     * @param   InstallationRef              $installation  The project's installation the message arrived through.
     * @param   IdentityRecord               $author        The account that wrote, as the platform describes it.
     * @param   InboundSupportMessage        $message       What they wrote.
     * @return  IngestedSupportMessage|null  The recorded message and the acknowledgement due, or null when the core dropped the message on purpose: support switched off, the member blocked, the member over the inbound allowance, or nothing to file.
     *
     * @throws  SupportRefused  When the installation names no project.
     */
    public function ingest(InstallationRef $installation, IdentityRecord $author, InboundSupportMessage $message): ?IngestedSupportMessage;

    /**
     * Record a creator's answer written on the platform, and have the core deliver it to the member.
     *
     * Delivery is the core's and is queued, never done inside this call: the
     * member is reached on the connector their thread is on, through the
     * `SupportRelay` port, and the message's delivery state is kept on the
     * row. The author is checked as the dashboard checks a reply: the
     * project's owner, or a member of the team it is shared with; null
     * records the answer as the owner's, for a message written somewhere only
     * the creator's staff can write (the project's linked relay space).
     *
     * @param   ConversationRef        $conversation  The thread being answered.
     * @param   CreatorRef|null        $author        The creator who wrote it, or null to record it as the project owner's.
     * @param   InboundSupportMessage  $message       What they wrote.
     * @param   SupportReplySource     $source        Where on the connector they wrote it.
     * @return  SupportMessageRef      The recorded reply.
     *
     * @throws  SupportRefused  When the thread does not exist, or the author may not answer on the project.
     */
    public function reply(ConversationRef $conversation, ?CreatorRef $author, InboundSupportMessage $message, SupportReplySource $source): SupportMessageRef;

    /**
     * The thread a creator is about to answer, when they may.
     *
     * For a connector that parks the thread a creator chose (a Reply button
     * on the relay's ping) and asks for their words next: the thread as the
     * connector may describe it, or null when it is gone or this creator may
     * not answer it, which the connector tells the creator in its own words.
     *
     * @param   string                           $conversationId  The thread id the connector carried, from the relay's ping.
     * @param   CreatorRef                       $author          The creator asking.
     * @return  SupportConversationSummary|null  The thread, or null when it is gone or not theirs to answer.
     */
    public function conversationForReply(string $conversationId, CreatorRef $author): ?SupportConversationSummary;

    /**
     * The thread a relay message announced, when a creator answers that message in place.
     *
     * The core remembers the platform id of every relay message it sent for a
     * month; a message that quotes one of them is an answer to the thread it
     * announced, and needs no button and no parked state.
     *
     * @param   string                           $externalChatId     The platform's id for the conversation the relay message was sent to.
     * @param   string                           $externalMessageId  The platform's id for the message the creator quoted.
     * @return  SupportConversationSummary|null  The thread, or null when the quoted message was no relay message the core still remembers.
     */
    public function conversationForRelayMessage(string $externalChatId, string $externalMessageId): ?SupportConversationSummary;

    /**
     * The thread mirrored into one thread of the project's relay space.
     *
     * @param   InstallationRef                  $installation  The project's installation the message arrived through.
     * @param   string                           $threadId      The platform's id for the thread inside the relay space.
     * @return  SupportConversationSummary|null  The thread, or null when the relay opened no such thread for this project.
     */
    public function conversationForThread(InstallationRef $installation, string $threadId): ?SupportConversationSummary;

    /**
     * Where the project behind an installation carries support to its creator.
     *
     * @param   InstallationRef          $installation  The project's installation.
     * @return  SupportRelayTarget|null  The mode in the manifest's words with the linked space, or null when the installation names no project.
     */
    public function relayTarget(InstallationRef $installation): ?SupportRelayTarget;

    /**
     * Record the place a project's support threads are mirrored into.
     *
     * Called when the platform has made the installation able to write there
     * (the bot was made an administrator of a group with topics); the core
     * never lets a creator type a place's id. Authorised as the project's
     * settings are, with the owner acting.
     *
     * @param  InstallationRef  $installation  The project's installation.
     * @param  SpaceRef         $space         The place, recorded through `Core\Spaces` when the connector keeps neutral rows, or the stand-in ref of its own row.
     *
     * @throws  SupportRefused  When the installation names no project, or nobody may act on it.
     */
    public function linkRelaySpace(InstallationRef $installation, SpaceRef $space): void;

    /**
     * Forget the project's relay space, once the installation can no longer write there.
     *
     * @param  InstallationRef  $installation  The project's installation.
     *
     * @throws  SupportRefused  When the installation names no project, or nobody may act on it.
     */
    public function unlinkRelaySpace(InstallationRef $installation): void;
}
