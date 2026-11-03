<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\CreatorTaskDetails;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Enums\CreatorTaskState;
use Subscriby\Connector\Exceptions\CreatorTasksRefused;

/**
 * A project's creator tasks as a connector's creator surface lists and settles them.
 *
 * A task opens when a member buys a plan that grants a place no connector
 * gates, a perk the creator hands over by hand, and marking it done issues
 * the grant and tells the member. Both calls take the `CreatorRef` of the
 * account talking and run as that creator under the dashboard's rules,
 * refused as `CreatorTasksRefused` with a stable reason otherwise.
 */
interface CreatorTasks
{
    /**
     * @param   CreatorRef                $creator  The creator asking.
     * @param   ProjectRef                $project  The project.
     * @param   CreatorTaskState          $state    Which tasks: the ones waiting, the ones done, or both.
     * @param   int                       $limit    How many at most, newest first.
     * @return  list<CreatorTaskDetails>  The tasks.
     *
     * @throws  CreatorTasksRefused  `creator_unknown` or `project_unknown`.
     */
    public function listForProject(CreatorRef $creator, ProjectRef $project, CreatorTaskState $state = CreatorTaskState::Open, int $limit = 20): array;

    /**
     * Mark a task done, which issues the grant behind it and tells the member.
     *
     * @param   CreatorRef          $creator  The creator settling it.
     * @param   string              $taskId   The task.
     * @return  CreatorTaskDetails  The task, done.
     *
     * @throws  CreatorTasksRefused  `creator_unknown`, `task_unknown`, `invalid` (already done, or the purchase behind it has ended) or `not_permitted`.
     */
    public function complete(CreatorRef $creator, string $taskId): CreatorTaskDetails;
}
