<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Data\ResourceDetails;
use Subscriby\Connector\Data\ResourceKind;
use Subscriby\Connector\Data\ResourceRef;
use Subscriby\Connector\Data\SpaceRef;
use Subscriby\Connector\Exceptions\ResourceRefused;

/**
 * The resource rows, as a connector may read them and create one from a place a creator picked.
 *
 * A space is a place the installation administers; a resource is the core's
 * decision to sell access to it. The one write here is that decision, made
 * the moment the platform answers a `SpaceCatalog` link request with the
 * chosen place: the connector records the place through `Spaces` and asks
 * for the resource here, and the core writes the row, binds it to the space
 * and announces it exactly as the dashboard would have.
 */
interface Resources
{
    /**
     * @param   string            $id  The resource row's UUID.
     * @return  ResourceRef|null  The resource, or null when none has that id.
     */
    public function find(string $id): ?ResourceRef;

    /**
     * The resource that sells a place, whichever project sells it.
     *
     * @param   SpaceRef          $space  The place.
     * @return  ResourceRef|null  The resource, or null when no project sells that place.
     */
    public function findBySpace(SpaceRef $space): ?ResourceRef;

    /**
     * @param   ProjectRef         $project    The project.
     * @param   string             $connector  The connector whose places are meant.
     * @return  list<ResourceRef>  The project's resources on that connector, oldest first; never null.
     */
    public function listForProject(ProjectRef $project, string $connector): array;

    /**
     * Sell access to a place: the resource for the project the creator picked it for.
     *
     * Idempotent on the project and the place: a place the project already
     * sells is answered with its resource rather than a second row, so a
     * connector may call this on every answer the platform gives. The write
     * acts for the creator the request runs as and is authorised as the
     * dashboard's own "add a resource" is; the core binds the row to the
     * space, broadcasts it to the dashboard and emits `project.resource.linked`.
     *
     * @param   ProjectRef    $project      The project the creator picked the place for.
     * @param   SpaceRef      $space        The place, recorded through `Spaces` first.
     * @param   ResourceKind  $kind         The stored kind, `connector:kind`, naming the place's connector.
     * @param   string        $title        The creator-facing title, usually the place's own.
     * @param   string|null   $description  What members get, in the creator's words, or null for none.
     * @return  ResourceRef   The resource, new or already there.
     *
     * @throws  ResourceRefused  When the kind is the manual perk or names another connector than the place's, the place or the project is unknown, or the actor may not add resources to the project.
     */
    public function create(ProjectRef $project, SpaceRef $space, ResourceKind $kind, string $title, ?string $description = null): ResourceRef;

    /**
     * Create the resource that sells a place, as the creator the ref names rather than whoever is acting.
     *
     * For a connector's creator surface, where no web request and no inbound
     * handler has set an actor: the same write as `create()`, run as the
     * creator under the dashboard's rules, so a creator who may not add
     * resources to the project is refused as "Add a resource" would refuse
     * them, and a project they may not open is unknown.
     *
     * @param   CreatorRef    $creator      The creator asking.
     * @param   ProjectRef    $project      The project.
     * @param   SpaceRef      $space        The place, recorded through `Spaces::record()` first.
     * @param   ResourceKind  $kind         The stored kind, `connector:kind`.
     * @param   string        $title        The creator-facing title.
     * @param   string|null   $description  What members get, or null for none.
     * @return  ResourceRef   The resource, new or the one already selling that place.
     *
     * @throws  ResourceRefused  `creator_unknown`, `project_unknown`, `kind_outside_place`, `place_unknown` or `not_permitted`.
     */
    public function createAs(CreatorRef $creator, ProjectRef $project, SpaceRef $space, ResourceKind $kind, string $title, ?string $description = null): ResourceRef;

    /**
     * A project's resources as its creator manages them, every connector's, with the standing the doctor recorded.
     *
     * @param   CreatorRef             $creator  The creator talking.
     * @param   ProjectRef             $project  The project.
     * @return  list<ResourceDetails>  The resources in storage order.
     *
     * @throws  ResourceRefused  When the creator or the project is unknown.
     */
    public function detailsForProject(CreatorRef $creator, ProjectRef $project): array;

    /**
     * Let plans grant a resource again, or stop them.
     *
     * @param   CreatorRef       $creator     The creator talking.
     * @param   string           $resourceId  The resource.
     * @param   bool             $active      True to let plans grant it, false to stop.
     * @return  ResourceDetails  The resource as written.
     *
     * @throws  ResourceRefused  When the creator or the resource is unknown, or the creator may not change it.
     */
    public function setActive(CreatorRef $creator, string $resourceId, bool $active): ResourceDetails;

    /**
     * Delete a resource, as the dashboard's own delete does; its plans keep running without it.
     *
     * @param  CreatorRef  $creator     The creator talking.
     * @param  string      $resourceId  The resource.
     *
     * @throws  ResourceRefused  When the creator or the resource is unknown, or the creator may not delete it.
     */
    public function delete(CreatorRef $creator, string $resourceId): void;
}
