<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\ConversationRef;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\DeliveryResult;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\OutboundSupportMessage;
use Subscriby\Connector\Data\Recipient;

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
}
