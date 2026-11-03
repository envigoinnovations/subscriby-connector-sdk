<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\ProjectDetails;
use Subscriby\Connector\Data\ProjectDraft;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Data\ProjectSummary;
use Subscriby\Connector\Exceptions\ProjectRefused;

/**
 * A creator's projects, as a connector's creator surface lists, opens, creates, edits and deletes them.
 *
 * Every call takes the `CreatorRef` of the account talking and runs as that
 * creator: the owner, or a teammate under the same team permissions and the
 * same tier limits the dashboard applies. A project the creator may not
 * open is unknown here, so a bot learns nothing the dashboard's own 404
 * would not tell, and every refusal reaches the connector as
 * `ProjectRefused` with a stable reason.
 */
interface Projects
{
    /**
     * @param   CreatorRef            $creator  The creator talking.
     * @return  list<ProjectSummary>  Every project the creator owns or shares through a team, in storage order.
     *
     * @throws  ProjectRefused  When the ref names no creator.
     */
    public function listForCreator(CreatorRef $creator): array;

    /**
     * @param   CreatorRef           $creator    The creator talking.
     * @param   string               $projectId  The project.
     * @return  ProjectDetails|null  The project as the creator may manage it, or null when no such project is theirs to open.
     *
     * @throws  ProjectRefused  When the ref names no creator.
     */
    public function find(CreatorRef $creator, string $projectId): ?ProjectDetails;

    /**
     * Create a project owned by the creator, under the limits of their plan.
     *
     * @param   CreatorRef      $creator  The creator talking.
     * @param   ProjectDraft    $draft    What the project is; the name is required, the rest optional.
     * @return  ProjectDetails  The project as written.
     *
     * @throws  ProjectRefused  When the creator is unknown, the plan allows no more projects, or a field is refused.
     */
    public function create(CreatorRef $creator, ProjectDraft $draft): ProjectDetails;

    /**
     * Change a project's settings; a field the draft never names keeps its value.
     *
     * @param   CreatorRef      $creator  The creator talking.
     * @param   ProjectRef      $project  The project.
     * @param   ProjectDraft    $draft    The fields to change.
     * @return  ProjectDetails  The project as written.
     *
     * @throws  ProjectRefused  When the creator or the project is unknown, a field is refused, or the creator may not change it.
     */
    public function update(CreatorRef $creator, ProjectRef $project, ProjectDraft $draft): ProjectDetails;

    /**
     * Delete a project and everything it owns, as the dashboard's own delete does.
     *
     * @param  CreatorRef  $creator  The creator talking.
     * @param  ProjectRef  $project  The project.
     *
     * @throws  ProjectRefused  When the creator or the project is unknown, or the creator may not delete it.
     */
    public function delete(CreatorRef $creator, ProjectRef $project): void;
}
