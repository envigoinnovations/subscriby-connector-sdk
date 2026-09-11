<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\SupportAttachmentKind;
use Subscriby\Connector\Exceptions\InvalidMessage;

/**
 * One file sent with a support reply.
 *
 * A file the creator uploaded is a URL the connector fetches; a file the
 * member sent through the same platform is handed back by the platform's own
 * reference, which costs no download and keeps the original quality. One of
 * the two must be present.
 */
final readonly class SupportAttachment
{
    /**
     * @param  SupportAttachmentKind  $kind            How the platform should show it.
     * @param  string|null            $url             Where the connector fetches it from, when it is stored by the application.
     * @param  string|null            $platformFileId  The platform's own reference to it, when it originated there.
     *
     * @throws  InvalidMessage  When neither a URL nor a platform reference is given.
     */
    public function __construct(
        public SupportAttachmentKind $kind,
        public ?string $url = null,
        public ?string $platformFileId = null,
    ) {
        if (($url === null || $url === '') && ($platformFileId === null || $platformFileId === '')) {
            throw InvalidMessage::because('an attachment needs a URL or a platform file reference');
        }
    }

    /**
     * @return  string  What the connector hands its platform: the URL when there is one, else the platform's own reference.
     */
    public function source(): string
    {
        return (string) ($this->url ?? $this->platformFileId);
    }
}
