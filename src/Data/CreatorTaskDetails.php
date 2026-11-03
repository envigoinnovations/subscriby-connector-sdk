<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * A creator task as a creator surface lists it: what a member bought that the creator grants by hand, and whether it is done.
 *
 * A task opens when a plan grants a place no connector gates (a manual
 * perk) and closes when the creator marks it done, which issues the grant;
 * the instructions are the creator's own words to themselves.
 */
final readonly class CreatorTaskDetails
{
    /**
     * @param  string                  $id             The task's id.
     * @param  string                  $projectId      The project.
     * @param  string|null             $memberId       The member who bought, when the purchase still stands.
     * @param  string                  $memberName     The member's name as the dashboard shows it.
     * @param  string|null             $resourceId     The place to grant, when it still exists.
     * @param  string                  $resourceTitle  The place's title.
     * @param  string                  $instructions   What to do, in the creator's words.
     * @param  DateTimeImmutable|null  $dueAt          When it should be done, when the creator set a date.
     * @param  DateTimeImmutable|null  $completedAt    When it was marked done, or null while it waits.
     * @param  DateTimeImmutable|null  $createdAt      When the purchase opened it.
     */
    public function __construct(
        public string $id,
        public string $projectId,
        public ?string $memberId,
        public string $memberName,
        public ?string $resourceId,
        public string $resourceTitle,
        public string $instructions,
        public ?DateTimeImmutable $dueAt,
        public ?DateTimeImmutable $completedAt,
        public ?DateTimeImmutable $createdAt,
    ) {}

    /**
     * @return  bool  True once the creator marked it done.
     */
    public function isDone(): bool
    {
        return $this->completedAt !== null;
    }
}
