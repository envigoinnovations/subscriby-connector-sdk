<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\SupportMessageKind;

/**
 * One file, or one piece of structured content, sent with a support message written on a connector.
 *
 * Only what the platform said about it travels, never the bytes: the core
 * keeps the platform's own reference and asks the connector's
 * `SupportRelay::fetchAttachment()` for the file the first time a creator
 * opens the thread, so a member's forty-megabyte video nobody looks at costs
 * nothing to store. A location or a contact has no file; what the platform
 * sent instead goes in `meta` under the platform's own keys.
 */
final readonly class InboundSupportAttachment
{
    /**
     * @param  SupportMessageKind    $kind             What the platform sent: a photo, a voice note, a document.
     * @param  string|null           $platformFileId   The platform's own reference to the file, or null when the kind carries none.
     * @param  string|null           $mime             The media type, when the platform reports one.
     * @param  string|null           $fileName         The file's name, when the platform reports one.
     * @param  int|null              $size             The size in bytes, when the platform reports it.
     * @param  int|null              $width            Pixels, for a picture or a video.
     * @param  int|null              $height           Pixels, for a picture or a video.
     * @param  int|null              $duration         Seconds, for audio and video.
     * @param  string|null           $thumbnailFileId  The platform's reference to a small preview, when it sends one apart from the file.
     * @param  array<string, mixed>  $meta             Anything else the platform said (a sticker's emoji, an audio title, coordinates, a contact's name and number).
     */
    public function __construct(
        public SupportMessageKind $kind,
        public ?string $platformFileId = null,
        public ?string $mime = null,
        public ?string $fileName = null,
        public ?int $size = null,
        public ?int $width = null,
        public ?int $height = null,
        public ?int $duration = null,
        public ?string $thumbnailFileId = null,
        public array $meta = [],
    ) {}
}
