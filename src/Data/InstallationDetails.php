<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\InstallationRole;
use Subscriby\Connector\Enums\InstallationScope;
use Subscriby\Connector\Enums\InstallationState;

/**
 * A project's installation as a creator surface lists it: which connector, what it is called there, and how the core last found it.
 */
final readonly class InstallationDetails
{
    /**
     * @param  string                  $id               The installation's id.
     * @param  string                  $connector        The connector key.
     * @param  string                  $connectorName    The connector's name as the directory shows it.
     * @param  InstallationScope       $scope            Project or platform.
     * @param  InstallationRole        $role             Live, or a standby kept for recovery.
     * @param  string                  $displayName      What the installation is called on the platform.
     * @param  string|null             $handle           Its handle there, when the platform has one.
     * @param  InstallationState       $state            How the core last found it.
     * @param  string|null             $stateReason      Why, in the connector's own code, when the state is not connected.
     * @param  DateTimeImmutable|null  $connectedAt      When it connected.
     * @param  DateTimeImmutable|null  $healthCheckedAt  When the doctor last looked.
     */
    public function __construct(
        public string $id,
        public string $connector,
        public string $connectorName,
        public InstallationScope $scope,
        public InstallationRole $role,
        public string $displayName,
        public ?string $handle,
        public InstallationState $state,
        public ?string $stateReason,
        public ?DateTimeImmutable $connectedAt,
        public ?DateTimeImmutable $healthCheckedAt,
    ) {}

    /**
     * @return  InstallationRef  The ref every other contract takes for it.
     */
    public function ref(): InstallationRef
    {
        return new InstallationRef($this->id, $this->connector, $this->scope, handle: $this->handle);
    }
}
