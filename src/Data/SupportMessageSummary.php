<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\SupportMessageAuthor;

/**
 * One message of a support conversation as a creator surface shows it: who wrote it, what it said, when.
 *
 * The text is the message as typed, without the quoted part a reply
 * carried; a file the member sent is told by its kind in the text so a
 * surface that cannot show it still says something was sent.
 */
final readonly class SupportMessageSummary
{
    /**
     * @param  string                $id          The message's id.
     * @param  SupportMessageAuthor  $author      Who wrote it.
     * @param  string|null           $authorName  The writer's name, when the core has one.
     * @param  string                $text        What it said.
     * @param  DateTimeImmutable     $sentAt      When it was written.
     */
    public function __construct(
        public string $id,
        public SupportMessageAuthor $author,
        public ?string $authorName,
        public string $text,
        public DateTimeImmutable $sentAt,
    ) {}
}
