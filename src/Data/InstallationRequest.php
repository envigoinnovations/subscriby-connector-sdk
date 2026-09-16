<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\InstallationScope;

/**
 * A creator asking to set a connector up on a project.
 *
 * `fields` are the values typed into the connector's declared install fields
 * (`SettingsSchema::installFields()`), already validated against the rules the
 * fields carried; the connector reads them by name. A connect that takes more
 * than one round arrives again with the same request grown by what the
 * creator answered next and the `state` the connector parked in its draft,
 * so `begin()` picks the flow up where it left it; `returnUrl` is where the
 * platform sends the creator back after a step taken there, for an OAuth
 * connector to use as its redirect target.
 */
final readonly class InstallationRequest
{
    /**
     * @param  InstallationScope     $scope      Platform-owned or a project's own.
     * @param  CreatorRef            $actor      Who is installing.
     * @param  ProjectRef|null       $project    The project, null for a platform installation.
     * @param  array<string, mixed>  $fields     The install fields, keyed by field name, every round's answers together.
     * @param  InstallationRef|null  $existing   The installation being reconnected under new credentials, whose connector row (its `storageRef`) is re-pointed rather than a second one created; null for a first connection.
     * @param  string|null           $returnUrl  The core's URL the platform sends the creator back to after a step taken on the platform; null when the core offers no return leg.
     * @param  array<string, mixed>  $state      What the connector parked in the previous draft's `state`, when the creator is answering a further round; empty on the first.
     */
    public function __construct(
        public InstallationScope $scope,
        public CreatorRef $actor,
        public ?ProjectRef $project,
        public array $fields = [],
        public ?InstallationRef $existing = null,
        public ?string $returnUrl = null,
        public array $state = [],
    ) {}
}
