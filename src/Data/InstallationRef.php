<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\InstallationScope;

/**
 * An installation: one connector set up on one project, or the platform's own presence on a connector.
 *
 * `storageRef` points at the connector's own row for this installation (the
 * Telegram connector keeps its bots in `telegram_bots`); the core stores it as
 * an opaque string and never joins on it, which is what lets a connector own
 * its tables.
 */
final readonly class InstallationRef
{
    /**
     * @param  string             $id          The installation row's UUID.
     * @param  string             $connector   The connector key.
     * @param  InstallationScope  $scope       Platform-owned or a project's own.
     * @param  string|null        $projectId   The project, null for a platform installation.
     * @param  string|null        $externalId  The platform's id for it (bot id, application id, workspace id).
     * @param  string|null        $storageRef  The connector's own row id for it, opaque to the core.
     * @param  string|null        $handle      The username or handle the platform shows for it, when it has one.
     */
    public function __construct(
        public string $id,
        public string $connector,
        public InstallationScope $scope,
        public ?string $projectId = null,
        public ?string $externalId = null,
        public ?string $storageRef = null,
        public ?string $handle = null,
    ) {}
}
