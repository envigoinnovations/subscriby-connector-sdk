<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing;

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
use Subscriby\Connector\Enums\ManagementCommand;
use Subscriby\Connector\Manifest\ManifestFile;
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
 * it. Its manifest is the `connector.json` beside this class, read through the
 * same loader every package goes through, so the loader runs in every test and
 * the file doubles as the worked example a connector author copies. Its port
 * fakes keep what they were asked in memory and offer assertions.
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

    private readonly ConnectorManifest $manifest;

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
        $this->manifest = ManifestFile::parse(array_replace(self::declaration(), ['key' => $this->key]), 'FakeConnector connector.json');
    }

    /**
     * @return  list<ManagementCommand>  The three commands the fake surface renders, so coverage gaps are visible.
     */
    public static function commands(): array
    {
        return [ManagementCommand::ProjectSettings, ManagementCommand::PlanManage, ManagementCommand::ResourceManage];
    }

    /**
     * @return  ConnectorManifest  The manifest from the `connector.json` beside this class, under this fake's key.
     */
    public function manifest(): ConnectorManifest
    {
        return $this->manifest;
    }

    /**
     * Bind every port the manifest promises, plus the ones every connector must bind.
     *
     * The settings form is bound here rather than declared in the file, so the
     * fake exercises the path a connector with installation-dependent fields
     * takes while a first-party package exercises the declared one.
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

    /**
     * @return  array<string, mixed>  The decoded `connector.json` beside this class.
     */
    private static function declaration(): array
    {
        $decoded = json_decode((string) file_get_contents(__DIR__.'/connector.json'), true, 32, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }
}
