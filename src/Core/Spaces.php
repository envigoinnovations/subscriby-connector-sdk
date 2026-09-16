<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\ResourceKind;
use Subscriby\Connector\Data\SpaceRecord;
use Subscriby\Connector\Data\SpaceRef;

/**
 * The space rows, as a connector may read and write them.
 *
 * A space is a place the installation administers; a resource is the core's
 * decision to sell access to it. Recording and binding are separate so a
 * connector can report every place it learns about without deciding anything
 * for the creator.
 */
interface Spaces
{
    /**
     * @param   string         $id  The space row's UUID.
     * @return  SpaceRef|null  The space, or null when none has that id.
     */
    public function find(string $id): ?SpaceRef;

    /**
     * @param   InstallationRef  $installation  The installation that administers the place.
     * @param   string           $kind          The connector's kind for it.
     * @param   string           $externalId    The platform's id for the place.
     * @return  SpaceRef|null    The space, or null when the place is unknown.
     */
    public function findByExternalId(InstallationRef $installation, string $kind, string $externalId): ?SpaceRef;

    /**
     * Write a space, or refresh the one already known.
     *
     * Idempotent on the installation, the kind and the platform id.
     *
     * @param   SpaceRecord  $record  What the platform says about the place.
     * @return  SpaceRef     The row, new or updated.
     */
    public function record(SpaceRecord $record): SpaceRef;

    /**
     * Point a resource at a space.
     *
     * Writes the resource's connector, kind and space in one step so the three
     * can never disagree; a resource bound to another space is re-pointed.
     *
     * @param  SpaceRef      $space       The place.
     * @param  string        $resourceId  The resource that sells access to it.
     * @param  ResourceKind  $kind        The stored kind, `connector:kind`.
     */
    public function bindResource(SpaceRef $space, string $resourceId, ResourceKind $kind): void;

    /**
     * Point a resource's standby at a space.
     *
     * A standby is the place kept ready to take a resource over, the same
     * kind of place as the resource; binding it names that place in neutral
     * shape so the recovery program never has to read the connector's rows.
     *
     * @param  SpaceRef  $space      The place.
     * @param  string    $standbyId  The standby kept ready for a resource.
     */
    public function bindStandby(SpaceRef $space, string $standbyId): void;
}
