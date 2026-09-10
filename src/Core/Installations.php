<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\InstallationRecord;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\ProjectRef;

/**
 * The installation rows, as a connector may read and write them.
 *
 * The Core API is the inverse of the ports: the application implements these
 * contracts and binds them in its container, and a connector calls them
 * instead of importing application classes, so a core refactor that keeps the
 * contract breaks no connector. Refs and records cross the boundary, never
 * models.
 */
interface Installations
{
    /**
     * @param   string                $id  The installation row's UUID.
     * @return  InstallationRef|null  The installation, or null when none has that id.
     */
    public function find(string $id): ?InstallationRef;

    /**
     * @param   string                $connector   The connector key.
     * @param   string                $externalId  The platform's id for the installation.
     * @return  InstallationRef|null  The installation, or null when the platform id is unknown.
     */
    public function findByExternalId(string $connector, string $externalId): ?InstallationRef;

    /**
     * @param   string                $connector   The connector key.
     * @param   string                $storageRef  The connector's own row id for the installation.
     * @return  InstallationRef|null  The installation, or null when nothing points at that row.
     */
    public function findByStorageRef(string $connector, string $storageRef): ?InstallationRef;

    /**
     * @param   ProjectRef             $project    The project.
     * @param   string|null            $connector  One connector's installations, or every connector's when null.
     * @return  list<InstallationRef>  The project's installations, live and standby.
     */
    public function listForProject(ProjectRef $project, ?string $connector = null): array;

    /**
     * @param   string                 $connector  The connector key.
     * @return  list<InstallationRef>  The platform-scope installations, the connector's shared presence.
     */
    public function listPlatform(string $connector): array;

    /**
     * Write an installation, or rewrite the one the platform already knows.
     *
     * Idempotent on the connector key and the platform id: a second record of
     * the same bot or app updates the row rather than adding one.
     *
     * @param   InstallationRecord  $record  What the installation is.
     * @return  InstallationRef     The row, new or updated.
     */
    public function record(InstallationRecord $record): InstallationRef;
}
