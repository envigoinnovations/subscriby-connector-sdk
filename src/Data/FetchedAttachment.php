<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The bytes of a file a member sent, as the connector hands them to the core.
 *
 * A stream rather than a string because a member can send a video of a size
 * nobody should hold in memory; the core copies it straight onto its own
 * disk. The name and mime are whatever the platform reported and may be
 * missing, so the core falls back to what it recorded when the message
 * arrived.
 */
final readonly class FetchedAttachment
{
    /**
     * @param  resource     $stream    A readable stream positioned at the start of the file; the core closes it.
     * @param  string|null  $fileName  The file's name on the platform, when it reports one.
     * @param  string|null  $mime      The file's mime type, when the platform reports one.
     */
    public function __construct(
        public mixed $stream,
        public ?string $fileName = null,
        public ?string $mime = null,
    ) {}
}
