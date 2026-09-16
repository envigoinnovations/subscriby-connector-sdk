<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\SupportMessageKind;

/**
 * A support message somebody wrote on a connector, as the connector read it off the platform.
 *
 * The same shape carries a member's question into the inbox and a creator's
 * answer written on the platform back to the thread; what differs is the
 * `Core\Support` call it is handed to. The platform's ids are what let the
 * core fold an edit into the message it already recorded, absorb a replayed
 * webhook, and quote the message a person answered.
 */
final readonly class InboundSupportMessage
{
    /**
     * @param  SupportMessageKind              $kind              What the message is; a captioned photo is a photo whose body is the caption.
     * @param  string|null                     $body              The text, or null for a bare file.
     * @param  list<InboundSupportAttachment>  $attachments       The files or structured content sent with it, by the platform's references.
     * @param  string|null                     $externalId        The platform's id for the message, so an edit or a replay updates rather than duplicates.
     * @param  string|null                     $externalChatId    The platform's id for the conversation it was written in.
     * @param  string|null                     $quotedExternalId  The platform's id for the message it answers, when the person quoted one.
     * @param  array<string, mixed>            $meta              Anything else worth keeping with it (a media group id, an edit flag).
     */
    public function __construct(
        public SupportMessageKind $kind,
        public ?string $body,
        public array $attachments = [],
        public ?string $externalId = null,
        public ?string $externalChatId = null,
        public ?string $quotedExternalId = null,
        public array $meta = [],
    ) {}

    /**
     * @param   string  $body  The text.
     * @return  self    A plain text message with no platform ids.
     */
    public static function text(string $body): self
    {
        return new self(SupportMessageKind::Text, $body);
    }

    /**
     * @return  bool  True when there is neither text nor anything attached, so nothing to file.
     */
    public function isEmpty(): bool
    {
        return ($this->body === null || trim($this->body) === '') && $this->attachments === [];
    }
}
