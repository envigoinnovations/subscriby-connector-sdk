<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing;

use DateTimeImmutable;
use Subscriby\Connector\Contracts\Connector;
use Subscriby\Connector\Contracts\ConnectorRegistrar;
use Subscriby\Connector\Contracts\Ports\AccessController;
use Subscriby\Connector\Contracts\Ports\FailureClassifier;
use Subscriby\Connector\Contracts\Ports\IdentityResolver;
use Subscriby\Connector\Contracts\Ports\InboundGateway;
use Subscriby\Connector\Contracts\Ports\InstallationLifecycle;
use Subscriby\Connector\Contracts\Ports\ManagementSurface;
use Subscriby\Connector\Contracts\Ports\Messenger;
use Subscriby\Connector\Contracts\Ports\PortalLoginMethod;
use Subscriby\Connector\Contracts\Ports\RecoverySupport;
use Subscriby\Connector\Contracts\Ports\SettingsSchema;
use Subscriby\Connector\Contracts\Ports\SpaceCatalog;
use Subscriby\Connector\Contracts\Ports\TextRenderer;
use Subscriby\Connector\Contracts\Ports\UiSlots;
use Subscriby\Connector\Data\ConnectorManifest;
use Subscriby\Connector\Data\Listing;
use Subscriby\Connector\Data\ListingLinks;
use Subscriby\Connector\Data\MessagingLimits;
use Subscriby\Connector\Data\Pacing;
use Subscriby\Connector\Data\RecoveryCapabilities;
use Subscriby\Connector\Data\ResourceKindDefinition;
use Subscriby\Connector\Enums\Capability;
use Subscriby\Connector\Enums\GrantMode;
use Subscriby\Connector\Enums\InstallationScope;
use Subscriby\Connector\Enums\InstallMode;
use Subscriby\Connector\Enums\ListingCategory;
use Subscriby\Connector\Enums\ManagementCommand;
use Subscriby\Connector\Testing\Fakes\FakeAccessController;
use Subscriby\Connector\Testing\Fakes\FakeFailureClassifier;
use Subscriby\Connector\Testing\Fakes\FakeIdentityResolver;
use Subscriby\Connector\Testing\Fakes\FakeInboundGateway;
use Subscriby\Connector\Testing\Fakes\FakeInstallationLifecycle;
use Subscriby\Connector\Testing\Fakes\FakeManagementSurface;
use Subscriby\Connector\Testing\Fakes\FakeMessenger;
use Subscriby\Connector\Testing\Fakes\FakePortalLoginMethod;
use Subscriby\Connector\Testing\Fakes\FakeRecoverySupport;
use Subscriby\Connector\Testing\Fakes\FakeSettingsSchema;
use Subscriby\Connector\Testing\Fakes\FakeSpaceCatalog;
use Subscriby\Connector\Testing\Fakes\FakeTextRenderer;
use Subscriby\Connector\Testing\Fakes\FakeUiSlots;

/**
 * A connector that exists to be tested against, and the reference implementation of the SDK.
 *
 * It deliberately violates every assumption a Telegram-shaped core would make:
 * messages are 280 characters with no files, access is a membership rather
 * than an invite link, one resource kind is a task the creator does by hand,
 * there is no early admission and the admin surface renders three commands out
 * of the catalogue. A core code path that still assumes Telegram fails against
 * it. Its port fakes keep what they were asked in memory and offer assertions.
 */
final class FakeConnector implements Connector
{
    public readonly FakeInstallationLifecycle $installations;

    public readonly FakeIdentityResolver $identities;

    public readonly FakeSpaceCatalog $spaces;

    public readonly FakeAccessController $access;

    public readonly FakeMessenger $messenger;

    public readonly FakeInboundGateway $inbound;

    public readonly FakeManagementSurface $management;

    public readonly FakeRecoverySupport $recovery;

    /**
     * @param  string  $key  The connector key; tests that register more than one fake give each its own.
     */
    public function __construct(
        private readonly string $key = 'fake',
    ) {
        $this->installations = new FakeInstallationLifecycle;
        $this->identities = new FakeIdentityResolver;
        $this->spaces = new FakeSpaceCatalog;
        $this->access = new FakeAccessController;
        $this->messenger = new FakeMessenger;
        $this->inbound = new FakeInboundGateway($this->key);
        $this->management = new FakeManagementSurface(self::commands());
        $this->recovery = new FakeRecoverySupport($this->key);
    }

    /**
     * @return  list<ManagementCommand>  The three commands the fake surface renders, so coverage gaps are visible.
     */
    public static function commands(): array
    {
        return [ManagementCommand::ProjectSettings, ManagementCommand::PlanManage, ManagementCommand::ResourceManage];
    }

    /**
     * @return  ConnectorManifest  A manifest shaped unlike Telegram in every dimension the SDK allows.
     */
    public function manifest(): ConnectorManifest
    {
        return new ConnectorManifest(
            key: $this->key,
            name: 'Fake Connector',
            version: '0.1.0',
            sdk: '^0.1',
            vendor: 'Subscriby',
            installMode: InstallMode::PasteCredential,
            scopes: [InstallationScope::Project, InstallationScope::Platform],
            resourceKinds: [
                new ResourceKindDefinition('room', 'Room', 'Private room', 'home', GrantMode::Membership),
                new ResourceKindDefinition('errand', 'Errand', 'Personal perk', 'clipboard-document-check', GrantMode::CreatorTask),
            ],
            capabilities: [
                Capability::Messaging,
                Capability::Broadcasts,
                Capability::AccessControl,
                Capability::ManagementSurface,
                Capability::PortalLogin,
                Capability::RecoveryProbes,
            ],
            messaging: new MessagingLimits(
                maxLength: 280,
                buttonsPerRow: 2,
                maxButtons: 4,
                callbackDataBytes: 32,
                supportsUnderline: false,
                supportsSpoiler: false,
                supportsFiles: false,
            ),
            pacing: new Pacing(minIntervalMicroseconds: 1_000_000, burst: 1, perRecipientIntervalMicroseconds: 1_000_000),
            managementCommands: self::commands(),
            relayModes: [],
            recovery: new RecoveryCapabilities(probes: true),
            listing: new Listing(
                category: ListingCategory::Community,
                tagline: 'A stand-in connector for tests and local demos',
                overview: 'Exercises every connector surface without talking to a real platform.',
                screenshots: [],
                links: new ListingLinks(documentation: 'https://docs.subscriby.net/connectors/building'),
                addedAt: new DateTimeImmutable('2026-09-10'),
                changelogUrl: null,
                signInRequired: false,
            ),
        );
    }

    /**
     * Bind every port the manifest promises, plus the ones every connector must bind.
     *
     * @param  ConnectorRegistrar  $registrar  The registry's collector.
     */
    public function register(ConnectorRegistrar $registrar): void
    {
        $registrar->port(InstallationLifecycle::class, $this->installations);
        $registrar->port(IdentityResolver::class, $this->identities);
        $registrar->port(SpaceCatalog::class, $this->spaces);
        $registrar->port(AccessController::class, $this->access);
        $registrar->port(Messenger::class, $this->messenger);
        $registrar->port(TextRenderer::class, new FakeTextRenderer);
        $registrar->port(InboundGateway::class, $this->inbound);
        $registrar->port(FailureClassifier::class, new FakeFailureClassifier);
        $registrar->port(ManagementSurface::class, $this->management);
        $registrar->port(PortalLoginMethod::class, new FakePortalLoginMethod);
        $registrar->port(RecoverySupport::class, $this->recovery);
        $registrar->port(SettingsSchema::class, new FakeSettingsSchema);
        $registrar->port(UiSlots::class, new FakeUiSlots);
    }
}
