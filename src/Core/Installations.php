<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\InstallationActionResult;
use Subscriby\Connector\Data\InstallationDetails;
use Subscriby\Connector\Data\InstallationRecord;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Enums\InstallationState;
use Subscriby\Connector\Exceptions\InstallationRefused;

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

    /**
     * The creator's settings for an installation, as the core stores them.
     *
     * The values of the fields the connector's `SettingsSchema` declares, by
     * name, written by the core's own settings action from the dashboard, the
     * REST API and the apps after the fields' own rules have run. A field the
     * creator never saved is absent, so the connector applies its own default;
     * the core tells the package of every change as well, through
     * `Events\InstallationSettingsChanged`.
     *
     * @param   InstallationRef       $installation  The installation.
     * @return  array<string, mixed>  Its settings by declared name; empty before any were saved, and for a ref the core does not know.
     */
    public function settings(InstallationRef $installation): array;

    /**
     * Write some of the creator's settings for an installation: the connector's own declared fields, checked by their rules.
     *
     * The one settings write a connector may make, for a value it learnt on
     * the platform (the channel an action just created) that the creator
     * would otherwise have to pick by hand. It runs through the same action
     * the dashboard's settings dialog does, for whoever is acting, so a
     * field the connector never declared and a value that fails its rule
     * are refused exactly as a creator's would be, and `InstallationSettingsChanged`
     * is dispatched afterwards as for any save.
     *
     * @param   InstallationRef       $installation  The installation.
     * @param   array<string, mixed>  $settings      The fields to write by declared name; the others keep their values.
     * @return  array<string, mixed>  Every setting as now stored.
     *
     * @throws  InstallationRefused  `installation_unknown`, `project_unknown` for a platform installation, `invalid` for a field the connector never declared or a value that fails its rule, or `not_permitted` when nobody who may change the project's connectors is acting.
     */
    public function updateSettings(InstallationRef $installation, array $settings): array;

    /**
     * Run one of the connector's own installation actions as the creator, from the connector's creator surface.
     *
     * The same action the dashboard's Connectors tab, the REST API and the
     * apps run, under the creator's tenant, so the project's permission to
     * change its connectors, the fields' rules and the installation's state
     * are checked by the core before the connector's port hears of it.
     *
     * @param   CreatorRef                $creator       The creator asking.
     * @param   InstallationRef           $installation  The installation acting.
     * @param   string                    $actionKey     The action's key, as the connector's `InstallationActions` port declares it.
     * @param   array<string, mixed>      $fields        The creator's answers by field name.
     * @return  InstallationActionResult  Done or refused, in the port's own words.
     *
     * @throws  InstallationRefused  `creator_unknown`, `installation_unknown`, `project_unknown`, `not_connected`, `action_unknown`, `invalid` or `not_permitted`.
     */
    public function runAction(CreatorRef $creator, InstallationRef $installation, string $actionKey, array $fields = []): InstallationActionResult;

    /**
     * A project's installations as the creator reads them: every connector's, with how the core last found each.
     *
     * @param   CreatorRef                 $creator  The creator asking.
     * @param   ProjectRef                 $project  The project.
     * @return  list<InstallationDetails>  The installations in storage order, live and standby alike.
     *
     * @throws  InstallationRefused  `creator_unknown` or `project_unknown`.
     */
    public function detailsForProject(CreatorRef $creator, ProjectRef $project): array;
}
