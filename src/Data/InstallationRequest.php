<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\InstallationScope;

/**
 * A creator asking to set a connector up on a project.
 *
 * `fields` are the values typed into the connector's declared install fields
 * (`SettingsSchema::installFields()`), already validated against the rules the
 * fields carried; the connector reads them by name.
 */
final readonly class InstallationRequest
{
    /**
     * @param  InstallationScope     $scope     Platform-owned or a project's own.
     * @param  CreatorRef            $actor     Who is installing.
     * @param  ProjectRef|null       $project   The project, null for a platform installation.
     * @param  array<string, mixed>  $fields    The install fields, keyed by field name.
     * @param  InstallationRef|null  $existing  The installation being reconnected under new credentials, whose connector row (its `storageRef`) is re-pointed rather than a second one created; null for a first connection.
     */
    public function __construct(
        public InstallationScope $scope,
        public CreatorRef $actor,
        public ?ProjectRef $project,
        public array $fields = [],
        public ?InstallationRef $existing = null,
    ) {}
}
