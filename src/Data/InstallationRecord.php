<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\InstallationRole;
use Subscriby\Connector\Enums\InstallationScope;
use Subscriby\Connector\Enums\InstallationState;

/**
 * Everything the core needs to write, or rewrite, one installation row.
 *
 * Handed to the Core API's `Installations::record()`, which is idempotent on
 * the connector key and the platform's own id for the installation: a
 * connector may describe the same bot or app as often as it likes and the
 * core keeps one row. Credentials are carried separately as a `CredentialBag`
 * so a record can be logged without ever holding a secret.
 */
final readonly class InstallationRecord
{
    /**
     * @param  string                  $connector    The connector key.
     * @param  InstallationScope       $scope        Platform-owned or a project's own.
     * @param  string                  $externalId   The platform's id for the installation (bot id, application id, workspace id).
     * @param  string                  $displayName  The name the platform reports.
     * @param  string|null             $projectId    The project, required for a project-scope installation and null for a platform one.
     * @param  InstallationRole        $role         Whether it acts or waits to take over.
     * @param  InstallationState       $state        Where it stands.
     * @param  string|null             $stateReason  A stable machine word for a degraded or revoked state.
     * @param  string|null             $handle       The username or handle, when the platform has one.
     * @param  string|null             $avatarUrl    A picture, when the platform has one.
     * @param  string|null             $storageRef   The connector's own row id for it, opaque to the core.
     * @param  CredentialBag|null      $credentials  The secrets to store, or null to leave the stored ones untouched.
     * @param  array<string, mixed>    $settings     The creator's settings for it.
     * @param  DateTimeImmutable|null  $connectedAt  When it was connected, or null when not yet.
     * @param  DateTimeImmutable|null  $verifiedAt   When the platform last confirmed it, or null when never.
     */
    public function __construct(
        public string $connector,
        public InstallationScope $scope,
        public string $externalId,
        public string $displayName,
        public ?string $projectId = null,
        public InstallationRole $role = InstallationRole::Live,
        public InstallationState $state = InstallationState::Connected,
        public ?string $stateReason = null,
        public ?string $handle = null,
        public ?string $avatarUrl = null,
        public ?string $storageRef = null,
        public ?CredentialBag $credentials = null,
        public array $settings = [],
        public ?DateTimeImmutable $connectedAt = null,
        public ?DateTimeImmutable $verifiedAt = null,
    ) {}
}
