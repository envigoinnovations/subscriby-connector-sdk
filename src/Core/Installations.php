<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\InstallationRecord;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Enums\InstallationState;

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
     * The secrets the core holds for an installation, as the ports receive them.
     *
     * The one read a connector makes before the core has handed it anything:
     * an inbound gateway verifying a signature needs the secret it minted at
     * `complete()`, which travelled through the summary's meta into this bag.
     * It answers only for an installation the connector can already name, so
     * it widens nothing a port call would not have given.
     *
     * @param   InstallationRef  $installation  The installation.
     * @return  CredentialBag    Its stored secrets, decrypted for this call; empty for a disconnected installation.
     */
    public function credentials(InstallationRef $installation): CredentialBag;

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

    /**
     * Record what the connector last found the installation's standing to be.
     *
     * The connector's health probes call this instead of re-describing the
     * whole installation, so a probe that only learnt "the token was revoked"
     * writes exactly that.
     *
     * @param  InstallationRef    $installation  The installation.
     * @param  InstallationState  $state         Connected, degraded, revoked or disconnected.
     * @param  string|null        $reason        The connector's own reason code, null when nothing is wrong.
     */
    public function recordState(InstallationRef $installation, InstallationState $state, ?string $reason = null): void;

    /**
     * Drop an installation whose row on the connector's side is gone.
     *
     * A mirror of a legacy deletion for the period in which the connector's
     * own tables lead and the neutral rows follow them, so the verification
     * counts stay honest. Once the neutral rows lead, disconnect and uninstall
     * keep the row and this call has no caller.
     *
     * @param  InstallationRef  $installation  The installation to drop.
     */
    public function forget(InstallationRef $installation): void;
}
