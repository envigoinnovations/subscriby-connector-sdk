<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A creator's support reply on its way to a member through a connector.
 */
final readonly class OutboundSupportMessage
{
    /**
     * @param  string                   $body              The canonical HTML of the reply.
     * @param  list<SupportAttachment>  $attachments       Files to send with it, in order.
     * @param  string|null              $quotedExternalId  The member's message being answered, when the platform can quote it.
     */
    public function __construct(
        public string $body,
        public array $attachments = [],
        public ?string $quotedExternalId = null,
    ) {}
}
