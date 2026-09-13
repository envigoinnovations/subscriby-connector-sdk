<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\Capability;
use Subscriby\Connector\Enums\InstallationScope;
use Subscriby\Connector\Enums\InstallMode;
use Subscriby\Connector\Enums\ManagementCommand;
use Subscriby\Connector\Exceptions\InvalidManifest;

/**
 * Everything the core knows about a connector without running it.
 *
 * The manifest is the contract between a connector package and the core: what
 * it can do, what it can gate, how it talks, how fast, which creator operations
 * it renders in-chat and how the directory should present it. It is validated
 * when built, so a connector that contradicts itself fails at boot rather than
 * in front of a creator. `official`, availability and the directory chips are
 * stamped by the registry from configuration and are never part of the
 * package.
 */
final readonly class ConnectorManifest
{
    /**
     * @param  string                        $key                 The connector key: lower-case letters, digits, `_` and `-`; also the table prefix and the config key.
     * @param  string                        $name                The display name.
     * @param  string                        $version             The package version, `major.minor.patch`.
     * @param  string                        $sdk                 The SDK constraint the package was built against, such as `^1.0`.
     * @param  string                        $vendor              Who publishes it.
     * @param  InstallMode                   $installMode         How a creator connects it.
     * @param  list<InstallationScope>       $scopes              The installation scopes it supports.
     * @param  list<ResourceKindDefinition>  $resourceKinds       The kinds of place it can gate.
     * @param  list<Capability>              $capabilities        What it declares it can do.
     * @param  MessagingLimits               $messaging           What one message may carry.
     * @param  Pacing                        $pacing              How fast the core may send through it.
     * @param  list<ManagementCommand>       $managementCommands  The creator operations its in-chat surface renders.
     * @param  list<string>                  $relayModes          The support relay modes it offers, in its own vocabulary.
     * @param  RecoveryCapabilities          $recovery            Which recovery facets it implements.
     * @param  Listing                       $listing             How the directory presents it.
     * @param  list<Field>                   $installFields       What a creator fills in to connect, in display order; empty when the connector binds its own settings schema.
     * @param  list<Field>                   $settingsFields      What a creator may change afterwards, without values; empty on the same condition.
     *
     * @throws  InvalidManifest  When the manifest contradicts itself or the SDK's rules.
     */
    public function __construct(
        public string $key,
        public string $name,
        public string $version,
        public string $sdk,
        public string $vendor,
        public InstallMode $installMode,
        public array $scopes,
        public array $resourceKinds,
        public array $capabilities,
        public MessagingLimits $messaging,
        public Pacing $pacing,
        public array $managementCommands,
        public array $relayModes,
        public RecoveryCapabilities $recovery,
        public Listing $listing,
        public array $installFields = [],
        public array $settingsFields = [],
    ) {
        $this->validate();
    }

    /**
     * @return  bool  True when the manifest declares an install or settings form, which the registry then serves for the connector.
     */
    public function declaresFields(): bool
    {
        return $this->installFields !== [] || $this->settingsFields !== [];
    }

    /**
     * @param   Capability  $capability  The capability asked about.
     * @return  bool        True when the manifest declares it.
     */
    public function supports(Capability $capability): bool
    {
        return in_array($capability, $this->capabilities, true);
    }

    /**
     * @param   ManagementCommand  $command  The command asked about.
     * @return  bool               True when the in-chat surface renders it.
     */
    public function supportsCommand(ManagementCommand $command): bool
    {
        return in_array($command, $this->managementCommands, true);
    }

    /**
     * @param   InstallationScope  $scope  The scope asked about.
     * @return  bool               True when installations of that scope exist for this connector.
     */
    public function supportsScope(InstallationScope $scope): bool
    {
        return in_array($scope, $this->scopes, true);
    }

    /**
     * @param   string                       $kind  The kind within this connector.
     * @return  ResourceKindDefinition|null  Its definition, or null when the connector does not gate such places.
     */
    public function kind(string $kind): ?ResourceKindDefinition
    {
        foreach ($this->resourceKinds as $definition) {
            if ($definition->kind === $kind) {
                return $definition;
            }
        }

        return null;
    }

    /**
     * The core commands this connector's surface does not render.
     *
     * @return  list<ManagementCommand>  The gaps the directory shows.
     */
    public function missingCommands(): array
    {
        return array_values(array_filter(
            ManagementCommand::core(),
            fn (ManagementCommand $command): bool => ! $this->supportsCommand($command),
        ));
    }

    /**
     * Refuse a manifest that contradicts itself.
     *
     * @throws  InvalidManifest  Naming the first contradiction found.
     */
    private function validate(): void
    {
        if (preg_match('/^[a-z][a-z0-9_-]*$/', $this->key) !== 1) {
            throw InvalidManifest::because($this->key, 'the key must be a lower-case identifier');
        }

        if (trim($this->name) === '' || trim($this->vendor) === '') {
            throw InvalidManifest::because($this->key, 'a name and a vendor are required');
        }

        if (preg_match('/^\d+\.\d+\.\d+/', $this->version) !== 1) {
            throw InvalidManifest::because($this->key, sprintf('version "%s" is not major.minor.patch', $this->version));
        }

        if (trim($this->sdk) === '') {
            throw InvalidManifest::because($this->key, 'the sdk constraint is required');
        }

        if ($this->scopes === []) {
            throw InvalidManifest::because($this->key, 'at least one installation scope is required');
        }

        $kinds = array_map(fn (ResourceKindDefinition $definition): string => $definition->kind, $this->resourceKinds);

        if (count($kinds) !== count(array_unique($kinds))) {
            throw InvalidManifest::because($this->key, 'resource kinds must be unique');
        }

        if ($this->resourceKinds !== [] && ! $this->supports(Capability::AccessControl)) {
            throw InvalidManifest::because($this->key, 'a connector that gates places must declare the access_control capability');
        }

        if ($this->supports(Capability::EarlyAdmissionHold) && ! $this->supports(Capability::AccessControl)) {
            throw InvalidManifest::because($this->key, 'early_admission_hold requires access_control');
        }

        if ($this->supports(Capability::Broadcasts) && ! $this->supports(Capability::Messaging)) {
            throw InvalidManifest::because($this->key, 'broadcasts requires messaging');
        }

        if ($this->managementCommands !== [] && ! $this->supports(Capability::ManagementSurface)) {
            throw InvalidManifest::because($this->key, 'a connector that renders management commands must declare the management_surface capability');
        }

        if ($this->relayModes !== [] && ! $this->supports(Capability::SupportRelay)) {
            throw InvalidManifest::because($this->key, 'a connector that offers relay modes must declare the support_relay capability');
        }

        foreach (Capability::cases() as $capability) {
            $isRecovery = str_starts_with($capability->value, 'recovery_');

            if ($isRecovery && $this->supports($capability) !== in_array($capability, $this->recovery->capabilities(), true)) {
                throw InvalidManifest::because($this->key, sprintf('the recovery block and the declared capabilities disagree about %s', $capability->value));
            }
        }
    }
}
