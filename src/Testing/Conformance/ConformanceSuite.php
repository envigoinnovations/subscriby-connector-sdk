<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Conformance;

use Closure;
use DateTimeImmutable;
use Illuminate\Http\Request;
use RuntimeException;
use Subscriby\Connector\Contracts\ConnectorRegistry;
use Subscriby\Connector\Contracts\NativePaymentProvider;
use Subscriby\Connector\Contracts\Ports\AccessController;
use Subscriby\Connector\Contracts\Ports\DataMigrator;
use Subscriby\Connector\Contracts\Ports\FailureClassifier;
use Subscriby\Connector\Contracts\Ports\IdentityResolver;
use Subscriby\Connector\Contracts\Ports\InboundGateway;
use Subscriby\Connector\Contracts\Ports\ManagementSurface;
use Subscriby\Connector\Contracts\Ports\PortalLoginMethod;
use Subscriby\Connector\Contracts\Ports\ProvidesPaymentMethods;
use Subscriby\Connector\Contracts\Ports\RecoverySupport;
use Subscriby\Connector\Contracts\Ports\SettingsSchema;
use Subscriby\Connector\Contracts\Ports\SupportRelay;
use Subscriby\Connector\Contracts\Ports\TextRenderer;
use Subscriby\Connector\Contracts\Ports\UiSlots;
use Subscriby\Connector\Data\BackfillOptions;
use Subscriby\Connector\Data\ConnectorManifest;
use Subscriby\Connector\Data\DeliveryFailure;
use Subscriby\Connector\Data\Field;
use Subscriby\Connector\Data\IdentitySummary;
use Subscriby\Connector\Data\InboundEnvelope;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Data\ReadinessItem;
use Subscriby\Connector\Data\ResourceKindDefinition;
use Subscriby\Connector\Data\SlotContribution;
use Subscriby\Connector\Enums\InstallationScope;
use Subscriby\Connector\Enums\InstallMode;
use Subscriby\Connector\Enums\ManagementCommand;
use Subscriby\Connector\Manifest\ManifestFile;
use Subscriby\Connector\Sdk;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * The rules every connector is measured against, run against one registered connector.
 *
 * The same suite runs against the first-party connectors and the fake inside
 * the application's own tests and against a third-party package in its own,
 * so "passes the kit" means one thing everywhere. Every rule is guarded: a
 * port that throws fails its rule with the exception's words and the rest of
 * the kit still runs, because an author fixing three things at once needs to
 * see all three.
 */
final class ConformanceSuite
{
    /** The canonical HTML the text rule renders. */
    public const string CANONICAL_SAMPLE = '<b>Bold</b> <i>italic</i> <u>underline</u> <s>struck</s> <a href="https://example.test">link</a> <code>code</code> <pre>pre</pre> <blockquote>quote</blockquote>';

    /**
     * @param  ConnectorRegistry  $registry  Where the connector and its ports are read from.
     */
    public function __construct(private readonly ConnectorRegistry $registry) {}

    /**
     * @param   string             $key          The connector key.
     * @param   string|null        $packagePath  The package root, when its migrations should be checked too.
     * @return  ConformanceReport  Every rule and whether it held.
     */
    public function run(string $key, ?string $packagePath = null): ConformanceReport
    {
        $manifest = $this->registry->manifest($key);

        $checks = [
            $this->guard('manifest.key_matches', fn (): ConformanceCheck => $manifest->key === $key
                ? ConformanceCheck::passed('manifest.key_matches')
                : ConformanceCheck::failed('manifest.key_matches', sprintf('the manifest says "%s", the registry says "%s"', $manifest->key, $key))),
            $this->guard('manifest.sdk_constraint', fn (): ConformanceCheck => Sdk::satisfies($manifest->sdk)
                ? ConformanceCheck::passed('manifest.sdk_constraint')
                : ConformanceCheck::failed('manifest.sdk_constraint', sprintf('SDK %s does not satisfy "%s"', Sdk::VERSION, $manifest->sdk))),
            $this->guard('manifest.listing_links', fn (): ConformanceCheck => $this->listingLinks($manifest)),
            $this->guard('manifest.resource_kinds', fn (): ConformanceCheck => $this->resourceKinds($manifest)),
            $this->guard('ports.required_bound', fn (): ConformanceCheck => ConformanceCheck::offenders(
                'ports.required_bound',
                array_values(array_filter(Sdk::REQUIRED_PORTS, fn (string $port): bool => ! $this->registry->binds($key, $port))),
                'ports every connector must bind:',
            )),
            $this->guard('settings.install_fields', fn (): ConformanceCheck => $this->installFields($key, $manifest)),
            $this->guard('settings.settings_fields', fn (): ConformanceCheck => $this->settingsFields($key)),
            $this->guard('text.plain_text_survives', fn (): ConformanceCheck => $this->plainText($key)),
            $this->guard('text.canonical_sample_renders', fn (): ConformanceCheck => $this->canonicalSample($key)),
            $this->guard('failures.classifies_anything', fn (): ConformanceCheck => $this->failures($key)),
            $this->guard('identity.tolerates_empty_envelope', fn (): ConformanceCheck => $this->identity($key)),
            $this->guard('inbound.tolerates_empty_request', fn (): ConformanceCheck => $this->inbound($key)),
            $this->guard('slots.well_formed', fn (): ConformanceCheck => $this->slots($key)),
        ];

        if ($this->registry->binds($key, AccessController::class)) {
            $checks[] = $this->guard('access.declares_kinds', fn (): ConformanceCheck => $manifest->resourceKinds !== []
                ? ConformanceCheck::passed('access.declares_kinds')
                : ConformanceCheck::failed('access.declares_kinds', 'access_control is declared but the manifest gates no resource kind'));
        }

        if ($this->registry->binds($key, ManagementSurface::class)) {
            $checks[] = $this->guard('management.commands_match_manifest', fn (): ConformanceCheck => $this->managementCommands($key, $manifest));
        }

        if ($this->registry->binds($key, SupportRelay::class)) {
            $checks[] = $this->guard('relay.modes_match_manifest', fn (): ConformanceCheck => $this->relayModes($key, $manifest));
        }

        if ($this->registry->binds($key, RecoverySupport::class)) {
            $checks[] = $this->guard('recovery.vocabulary_and_readiness', fn (): ConformanceCheck => $this->recovery($key));
        }

        if ($this->registry->binds($key, PortalLoginMethod::class)) {
            $checks[] = $this->guard('portal_login.button', fn (): ConformanceCheck => $this->portalLogin($key));
        }

        if ($this->registry->binds($key, ProvidesPaymentMethods::class)) {
            $checks[] = $this->guard('payments.provider_keys', fn (): ConformanceCheck => $this->paymentProviders($key));
        }

        if ($this->registry->binds($key, DataMigrator::class)) {
            $checks[] = $this->guard('migration.reports_for_connector', fn (): ConformanceCheck => $this->migrator($key));
        }

        if ($packagePath !== null) {
            $checks[] = $this->guard('manifest.file_is_the_source', fn (): ConformanceCheck => $this->manifestFile($manifest, rtrim($packagePath, '/\\').'/'.ManifestFile::FILENAME));
            $checks[] = $this->guard('migrations.own_tables_only', fn (): ConformanceCheck => ConformanceCheck::offenders(
                'migrations.own_tables_only',
                MigrationRules::violations($key, rtrim($packagePath, '/\\').'/database/migrations'),
                'a connector creates only tables prefixed with its key and never alters a core table:',
            ));
        }

        return new ConformanceReport($key, $checks);
    }

    /**
     * @param   ConnectorManifest  $registered  What the registry holds.
     * @param   string             $path        The package's `connector.json`.
     * @return  ConformanceCheck   The file exists, loads, and is what the registry holds.
     */
    private function manifestFile(ConnectorManifest $registered, string $path): ConformanceCheck
    {
        if (! is_file($path)) {
            return ConformanceCheck::failed('manifest.file_is_the_source', sprintf('%s does not exist; the manifest must be declared in the file', $path));
        }

        $loaded = ManifestFile::load($path);
        $offenders = [];

        foreach (['key' => [$loaded->key, $registered->key], 'name' => [$loaded->name, $registered->name], 'version' => [$loaded->version, $registered->version], 'sdk' => [$loaded->sdk, $registered->sdk]] as $field => [$fromFile, $fromRegistry]) {
            if ($fromFile !== $fromRegistry) {
                $offenders[] = sprintf('%s is "%s" in the file and "%s" in the registry', $field, $fromFile, $fromRegistry);
            }
        }

        if (count($loaded->capabilities) !== count($registered->capabilities)) {
            $offenders[] = 'the capabilities differ between the file and the registry';
        }

        if (count($loaded->installFields) !== count($registered->installFields) || count($loaded->settingsFields) !== count($registered->settingsFields)) {
            $offenders[] = 'the declared fields differ between the file and the registry';
        }

        return ConformanceCheck::offenders('manifest.file_is_the_source', $offenders, 'the registered manifest must come from the file:');
    }

    /**
     * @param   ConnectorManifest  $manifest  The manifest.
     * @return  ConformanceCheck   Every listing link is an absolute https URL or a mailto address.
     */
    private function listingLinks(ConnectorManifest $manifest): ConformanceCheck
    {
        $links = $manifest->listing->links;
        $offenders = [];

        foreach ([
            'documentation' => $links->documentation,
            'support' => $links->support,
            'privacy' => $links->privacy,
            'terms' => $links->terms,
            'homepage' => $links->homepage,
            'changelog' => $manifest->listing->changelogUrl,
        ] as $name => $link) {
            if ($link !== null && preg_match('#^(https://[^\s/]+|mailto:[^\s@]+@[^\s]+)#', $link) !== 1) {
                $offenders[] = sprintf('%s "%s"', $name, $link);
            }
        }

        return ConformanceCheck::offenders('manifest.listing_links', $offenders, 'links must be https URLs or mailto addresses:');
    }

    /**
     * @param   ConnectorManifest  $manifest  The manifest.
     * @return  ConformanceCheck   Every resource kind has a label, a portal label and an icon.
     */
    private function resourceKinds(ConnectorManifest $manifest): ConformanceCheck
    {
        $offenders = [];

        foreach ($manifest->resourceKinds as $definition) {
            if (! $definition instanceof ResourceKindDefinition) {
                $offenders[] = 'a resource kind that is not a ResourceKindDefinition';

                continue;
            }

            foreach (['label' => $definition->label, 'portalLabel' => $definition->portalLabel, 'icon' => $definition->icon] as $field => $value) {
                if (trim($value) === '') {
                    $offenders[] = sprintf('%s has no %s', $definition->kind, $field);
                }
            }
        }

        return ConformanceCheck::offenders('manifest.resource_kinds', $offenders, 'resource kinds:');
    }

    /**
     * @param   string             $key       The connector key.
     * @param   ConnectorManifest  $manifest  The manifest.
     * @return  ConformanceCheck   The install form has unique names, labels, and a secret for a paste-a-credential connector.
     */
    private function installFields(string $key, ConnectorManifest $manifest): ConformanceCheck
    {
        $fields = $this->registry->port($key, SettingsSchema::class)->installFields();
        $offenders = $this->fieldOffenders($fields);
        $inputs = array_filter($fields, fn (Field $field): bool => $field->type->isInput());

        if ($manifest->installMode === InstallMode::PasteCredential) {
            if ($inputs === []) {
                $offenders[] = 'a paste-a-credential connector declares no input field';
            }

            if (! array_any($inputs, fn (Field $field): bool => $field->type->value === 'secret')) {
                $offenders[] = 'a paste-a-credential connector declares no secret field';
            }
        }

        return ConformanceCheck::offenders('settings.install_fields', $offenders, 'install fields:');
    }

    /**
     * @param   string            $key  The connector key.
     * @return  ConformanceCheck  The settings form, asked for the defaults, is a well-formed field list.
     */
    private function settingsFields(string $key): ConformanceCheck
    {
        $fields = $this->registry->port($key, SettingsSchema::class)->settingsFields(null);

        return ConformanceCheck::offenders('settings.settings_fields', $this->fieldOffenders($fields), 'settings fields:');
    }

    /**
     * @param   list<mixed>   $fields  What a schema returned.
     * @return  list<string>  Duplicated names, non-fields and unlabelled inputs.
     */
    private function fieldOffenders(array $fields): array
    {
        $offenders = [];
        $seen = [];

        foreach ($fields as $field) {
            if (! $field instanceof Field) {
                $offenders[] = 'an entry that is not a Field';

                continue;
            }

            if (isset($seen[$field->name])) {
                $offenders[] = sprintf('"%s" is declared twice', $field->name);
            }

            $seen[$field->name] = true;

            if (trim($field->label) === '') {
                $offenders[] = sprintf('"%s" has no label', $field->name);
            }
        }

        return $offenders;
    }

    /**
     * @param   string            $key  The connector key.
     * @return  ConformanceCheck  Text with no markup renders as itself.
     */
    private function plainText(string $key): ConformanceCheck
    {
        $rendered = $this->registry->port($key, TextRenderer::class)->render('Hello, world');

        return $rendered === 'Hello, world'
            ? ConformanceCheck::passed('text.plain_text_survives')
            : ConformanceCheck::failed('text.plain_text_survives', sprintf('"Hello, world" rendered as "%s"', $rendered));
    }

    /**
     * @param   string            $key  The connector key.
     * @return  ConformanceCheck  The canonical subset renders to a non-empty string that keeps the words.
     */
    private function canonicalSample(string $key): ConformanceCheck
    {
        $rendered = $this->registry->port($key, TextRenderer::class)->render(self::CANONICAL_SAMPLE);
        $missing = array_values(array_filter(['Bold', 'italic', 'underline', 'struck', 'link', 'code', 'pre', 'quote'], fn (string $word): bool => ! str_contains($rendered, $word)));

        if (trim($rendered) === '') {
            return ConformanceCheck::failed('text.canonical_sample_renders', 'the canonical sample rendered to nothing');
        }

        return ConformanceCheck::offenders('text.canonical_sample_renders', $missing, 'words lost in rendering:');
    }

    /**
     * @param   string            $key  The connector key.
     * @return  ConformanceCheck  A throwable and an unknown value both classify to a failure.
     */
    private function failures(string $key): ConformanceCheck
    {
        $classifier = $this->registry->port($key, FailureClassifier::class);
        $offenders = [];

        foreach (['a throwable' => new RuntimeException('conformance probe'), 'an unknown value' => 'conformance probe'] as $label => $input) {
            $failure = $classifier->classify($input);

            if (! $failure instanceof DeliveryFailure) {
                $offenders[] = $label.' did not classify to a DeliveryFailure';
            }
        }

        return ConformanceCheck::offenders('failures.classifies_anything', $offenders, 'the classifier:');
    }

    /**
     * @param   string            $key  The connector key.
     * @return  ConformanceCheck  An envelope with nothing in it resolves to nobody rather than throwing.
     */
    private function identity(string $key): ConformanceCheck
    {
        $summary = $this->registry->port($key, IdentityResolver::class)->resolveInbound(
            new InboundEnvelope($key, null, 'conformance', 'unknown', [], new DateTimeImmutable),
        );

        return $summary === null || $summary instanceof IdentitySummary
            ? ConformanceCheck::passed('identity.tolerates_empty_envelope')
            : ConformanceCheck::failed('identity.tolerates_empty_envelope', 'resolveInbound() returned something other than null or an IdentitySummary');
    }

    /**
     * @param   string            $key  The connector key.
     * @return  ConformanceCheck  An empty JSON call authenticates to a boolean, decodes to an iterable and answers null or a response.
     */
    private function inbound(string $key): ConformanceCheck
    {
        $gateway = $this->registry->port($key, InboundGateway::class);
        $request = Request::create('/conformance', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], '{}');
        $offenders = [];

        if (! is_bool($gateway->authenticate($request))) {
            $offenders[] = 'authenticate() did not return a boolean';
        }

        if (! is_iterable($gateway->decode($request))) {
            $offenders[] = 'decode() did not return an iterable';
        }

        $immediate = $gateway->immediateResponse($request);

        if ($immediate !== null && ! $immediate instanceof Response) {
            $offenders[] = 'immediateResponse() returned something other than null or a Response';
        }

        return ConformanceCheck::offenders('inbound.tolerates_empty_request', $offenders, 'the gateway:');
    }

    /**
     * @param   string            $key  The connector key.
     * @return  ConformanceCheck  Each slot is filled once and every contribution ships a placeholder.
     */
    private function slots(string $key): ConformanceCheck
    {
        $offenders = [];
        $seen = [];

        foreach ($this->registry->port($key, UiSlots::class)->slots() as $contribution) {
            if (! $contribution instanceof SlotContribution) {
                $offenders[] = 'an entry that is not a SlotContribution';

                continue;
            }

            if (isset($seen[$contribution->slot->value])) {
                $offenders[] = sprintf('%s is filled twice', $contribution->slot->value);
            }

            $seen[$contribution->slot->value] = true;

            if ($contribution->placeholder === null || trim($contribution->placeholder) === '') {
                $offenders[] = sprintf('%s ships no placeholder', $contribution->slot->value);
            }
        }

        return ConformanceCheck::offenders('slots.well_formed', $offenders, 'slots:');
    }

    /**
     * @param   string             $key       The connector key.
     * @param   ConnectorManifest  $manifest  The manifest.
     * @return  ConformanceCheck   The surface renders exactly the commands the manifest lists.
     */
    private function managementCommands(string $key, ConnectorManifest $manifest): ConformanceCheck
    {
        $rendered = array_map(fn (ManagementCommand $command): string => $command->value, $this->registry->port($key, ManagementSurface::class)->commands());
        $declared = array_map(fn (ManagementCommand $command): string => $command->value, $manifest->managementCommands);

        sort($rendered);
        sort($declared);

        return $rendered === $declared
            ? ConformanceCheck::passed('management.commands_match_manifest')
            : ConformanceCheck::failed('management.commands_match_manifest', sprintf('renders [%s], manifest declares [%s]', implode(', ', $rendered), implode(', ', $declared)));
    }

    /**
     * @param   string             $key       The connector key.
     * @param   ConnectorManifest  $manifest  The manifest.
     * @return  ConformanceCheck   The relay offers exactly the modes the manifest lists.
     */
    private function relayModes(string $key, ConnectorManifest $manifest): ConformanceCheck
    {
        $offered = $this->registry->port($key, SupportRelay::class)->relayModes();
        $declared = $manifest->relayModes;

        sort($offered);
        sort($declared);

        return $offered === $declared
            ? ConformanceCheck::passed('relay.modes_match_manifest')
            : ConformanceCheck::failed('relay.modes_match_manifest', sprintf('offers [%s], manifest declares [%s]', implode(', ', $offered), implode(', ', $declared)));
    }

    /**
     * @param   string            $key  The connector key.
     * @return  ConformanceCheck  The vocabulary names every noun and the readiness checks are well-formed items with unique keys.
     */
    private function recovery(string $key): ConformanceCheck
    {
        $recovery = $this->registry->port($key, RecoverySupport::class);
        $vocabulary = $recovery->vocabulary();
        $offenders = [];

        foreach (['installationNoun' => $vocabulary->installationNoun, 'spaceNoun' => $vocabulary->spaceNoun, 'identityNoun' => $vocabulary->identityNoun, 'grantNoun' => $vocabulary->grantNoun] as $name => $noun) {
            if (trim($noun) === '') {
                $offenders[] = sprintf('vocabulary %s is empty', $name);
            }
        }

        $seen = [];
        $installation = new InstallationRef('00000000-0000-0000-0000-000000000000', $key, InstallationScope::Project, '00000000-0000-0000-0000-000000000001');

        foreach ($recovery->readinessChecks($installation, new ProjectRef('00000000-0000-0000-0000-000000000001')) as $item) {
            if (! $item instanceof ReadinessItem) {
                $offenders[] = 'a readiness entry that is not a ReadinessItem';

                continue;
            }

            if (isset($seen[$item->key])) {
                $offenders[] = sprintf('readiness key "%s" is declared twice', $item->key);
            }

            $seen[$item->key] = true;
        }

        return ConformanceCheck::offenders('recovery.vocabulary_and_readiness', $offenders, 'recovery:');
    }

    /**
     * @param   string            $key  The connector key.
     * @return  ConformanceCheck  The sign-in button has a label and an icon.
     */
    private function portalLogin(string $key): ConformanceCheck
    {
        $button = $this->registry->port($key, PortalLoginMethod::class)->button();
        $offenders = [];

        if (trim($button->label) === '') {
            $offenders[] = 'the button has no label';
        }

        if (trim($button->icon) === '') {
            $offenders[] = 'the button has no icon';
        }

        return ConformanceCheck::offenders('portal_login.button', $offenders, 'portal login:');
    }

    /**
     * @param   string            $key  The connector key.
     * @return  ConformanceCheck  Every provider is keyed `connector:provider`, labelled and settles in at least one currency.
     */
    private function paymentProviders(string $key): ConformanceCheck
    {
        $offenders = [];

        foreach ($this->registry->port($key, ProvidesPaymentMethods::class)->paymentProviders() as $provider) {
            if (! $provider instanceof NativePaymentProvider) {
                $offenders[] = 'an entry that is not a NativePaymentProvider';

                continue;
            }

            if (preg_match('/^'.preg_quote($key, '/').':[a-z][a-z0-9_-]*$/', $provider->key()) !== 1) {
                $offenders[] = sprintf('"%s" is not keyed %s:provider', $provider->key(), $key);
            }

            if (trim($provider->label()) === '') {
                $offenders[] = sprintf('"%s" has no label', $provider->key());
            }

            if ($provider->currencies() === []) {
                $offenders[] = sprintf('"%s" settles in no currency', $provider->key());
            }
        }

        return ConformanceCheck::offenders('payments.provider_keys', $offenders, 'payment providers:');
    }

    /**
     * @param   string            $key  The connector key.
     * @return  ConformanceCheck  A dry run and a verification both report for this connector, and the dry run says so.
     */
    private function migrator(string $key): ConformanceCheck
    {
        $migrator = $this->registry->port($key, DataMigrator::class);
        $backfill = $migrator->backfill(new BackfillOptions(dryRun: true));
        $verification = $migrator->verify();
        $offenders = [];

        if ($backfill->connector !== $key) {
            $offenders[] = sprintf('the backfill report names "%s"', $backfill->connector);
        }

        if (! $backfill->dryRun) {
            $offenders[] = 'the dry run did not report itself as one';
        }

        if ($verification->connector !== $key) {
            $offenders[] = sprintf('the verification report names "%s"', $verification->connector);
        }

        return ConformanceCheck::offenders('migration.reports_for_connector', $offenders, 'the migrator:');
    }

    /**
     * Run one rule, turning a port that throws into a failed rule that quotes it.
     *
     * @param   string                       $name  The rule.
     * @param   Closure(): ConformanceCheck  $rule  The rule.
     * @return  ConformanceCheck             The outcome.
     */
    private function guard(string $name, Closure $rule): ConformanceCheck
    {
        try {
            return $rule();
        } catch (Throwable $exception) {
            return $this->error($name, $exception);
        }
    }

    /**
     * @param   string            $name       The rule that threw.
     * @param   Throwable         $exception  What it threw.
     * @return  ConformanceCheck  The rule, failed with the exception's words so the author sees them.
     */
    private function error(string $name, Throwable $exception): ConformanceCheck
    {
        return ConformanceCheck::failed($name, sprintf('threw %s: %s', $exception::class, $exception->getMessage()));
    }
}
