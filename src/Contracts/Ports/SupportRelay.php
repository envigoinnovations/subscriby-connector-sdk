<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\ConversationRef;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\DeliveryResult;
use Subscriby\Connector\Data\FetchedAttachment;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\Message;
use Subscriby\Connector\Data\OutboundSupportMessage;
use Subscriby\Connector\Data\Recipient;
use Subscriby\Connector\Data\RelayThread;
use Subscriby\Connector\Data\SpaceRef;
use Subscriby\Connector\Data\SupportAttachment;

/**
 * Carrying a support conversation between a member and a creator over the connector.
 *
 * Bound by connectors that declare `support_relay`. The inbox is core; the
 * connector delivers the creator's replies to the member and offers the relay
 * modes its platform makes possible (a DM to the creator, a topic in a group),
 * named in its own vocabulary and listed in the manifest.
 */
interface SupportRelay
{
    /**
     * @return  list<string>  The relay modes offered, matching the manifest's `relayModes`.
     */
    public function relayModes(): array;

    /**
     * Deliver a creator's reply to the member.
     *
     * @param   InstallationRef         $installation  The project's installation.
     * @param   CredentialBag           $credentials   Its secrets.
     * @param   ConversationRef         $conversation  The thread being answered.
     * @param   Recipient               $recipient     The member.
     * @param   OutboundSupportMessage  $message       The reply.
     * @return  DeliveryResult          Delivered with the platform's message id, or the classified failure.
     */
    public function relay(InstallationRef $installation, CredentialBag $credentials, ConversationRef $conversation, Recipient $recipient, OutboundSupportMessage $message): DeliveryResult;

    /**
     * Open a thread for a conversation in the space the creator relays support into.
     *
     * The core keeps the thread's id on the conversation and passes it to
     * every later post, so a platform that threads conversations shows each
     * member's thread in one place.
     *
     * @param   InstallationRef  $installation  The project's installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   SpaceRef         $space         The relay space: the group, channel or forum the creator linked.
     * @param   string           $title         The thread's name, usually the member's.
     * @return  RelayThread      The thread's id, or the classified refusal.
     */
    public function openThread(InstallationRef $installation, CredentialBag $credentials, SpaceRef $space, string $title): RelayThread;

    /**
     * Post into a conversation's thread in the relay space.
     *
     * @param   InstallationRef  $installation  The project's installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   SpaceRef         $space         The relay space.
     * @param   string           $threadId      The thread `openThread()` answered with.
     * @param   Message          $message       What to post.
     * @return  DeliveryResult   Delivered with the platform's message id, or the classified failure; a thread the platform no longer knows is `TargetMissing`.
     */
    public function postToThread(InstallationRef $installation, CredentialBag $credentials, SpaceRef $space, string $threadId, Message $message): DeliveryResult;

    /**
     * Hand the core the bytes of a file a member sent.
     *
     * The core keeps a member's file as the platform's own reference until a
     * creator opens the thread, then asks for it once and stores its own copy.
     * A connector whose platform serves files at a URL fetches it; one whose
     * Bot API runs in local mode is given an absolute path on that server's
     * disk and reads it from a mounted copy of the directory. Null when the
     * platform cannot hand the file over (a reference it no longer knows, a
     * path this container cannot see), with the reason logged by the
     * connector; the core answers "not found" and stores nothing.
     *
     * @param   InstallationRef         $installation  The project's installation the file was received through.
     * @param   CredentialBag           $credentials   Its secrets.
     * @param   SupportAttachment       $attachment    The file, by its platform reference.
     * @return  FetchedAttachment|null  The bytes, or null when the platform cannot hand them over.
     */
    public function fetchAttachment(InstallationRef $installation, CredentialBag $credentials, SupportAttachment $attachment): ?FetchedAttachment;
}
