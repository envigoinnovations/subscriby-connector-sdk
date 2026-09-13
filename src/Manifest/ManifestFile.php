<?php

declare(strict_types=1);

namespace Subscriby\Connector\Manifest;

use DateTimeImmutable;
use JsonException;
use Subscriby\Connector\Data\ConnectorManifest;
use Subscriby\Connector\Data\Field;
use Subscriby\Connector\Data\Listing;
use Subscriby\Connector\Data\ListingLinks;
use Subscriby\Connector\Data\ListingMarketing;
use Subscriby\Connector\Data\MessagingLimits;
use Subscriby\Connector\Data\Pacing;
use Subscriby\Connector\Data\RecoveryCapabilities;
use Subscriby\Connector\Data\ResourceKindDefinition;
use Subscriby\Connector\Enums\Capability;
use Subscriby\Connector\Enums\FieldType;
use Subscriby\Connector\Enums\GrantMode;
use Subscriby\Connector\Enums\InstallationScope;
use Subscriby\Connector\Enums\InstallMode;
use Subscriby\Connector\Enums\ListingCategory;
use Subscriby\Connector\Enums\ManagementCommand;
use Subscriby\Connector\Exceptions\InvalidManifest;

/**
 * Reads a connector's `connector.json` into the typed manifest.
 *
 * The file is the source of truth for what a connector is: the directory, the
 * review pipeline and the mobile apps can read it from a repository without
 * installing or booting the package, and its author edits data rather than
 * code. The shape is published as `connector.schema.json` beside this class
 * for editors and CI; the same rules are checked here, in PHP and without a
 * dependency, so a package that disagrees with its own schema fails at boot
 * with every problem listed at once. Strings the creator reads (labels, steps,
 * the tagline, the overview) are English keys the package translates through
 * its own language files at render time.
 */
final class ManifestFile
{
    /** The file a connector package keeps at its root. */
    public const string FILENAME = 'connector.json';

    /** The top-level keys the file may carry; anything else is a typo the loader refuses. */
    public const array TOP_LEVEL_KEYS = [
        '$schema', 'key', 'name', 'version', 'sdk', 'vendor', 'install', 'resource_kinds', 'capabilities',
        'messaging', 'pacing', 'management_commands', 'relay_modes', 'recovery', 'listing',
    ];

    /**
     * Static only: the loader keeps no state between files.
     */
    private function __construct() {}

    /**
     * Read and check a package's manifest file.
     *
     * @param   string             $path  The absolute path of the `connector.json`.
     * @return  ConnectorManifest  The manifest, install and settings fields included.
     *
     * @throws  InvalidManifest  When the file is missing, is not JSON, or breaks a rule.
     */
    public static function load(string $path): ConnectorManifest
    {
        if (! is_file($path)) {
            throw InvalidManifest::because('unknown', sprintf('%s does not exist', $path));
        }

        try {
            $data = json_decode((string) file_get_contents($path), true, 32, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw InvalidManifest::because('unknown', sprintf('%s is not valid JSON: %s', $path, $exception->getMessage()));
        }

        if (! is_array($data) || ($data !== [] && array_is_list($data))) {
            throw InvalidManifest::because('unknown', sprintf('%s must hold a JSON object', $path));
        }

        return self::parse($data, $path);
    }

    /**
     * Check decoded manifest data and build the typed manifest from it.
     *
     * @param   array<string, mixed>  $data    The decoded file.
     * @param   string                $source  Where it came from, for messages.
     * @return  ConnectorManifest     The manifest, install and settings fields included.
     *
     * @throws  InvalidManifest  Listing every rule the data breaks.
     */
    public static function parse(array $data, string $source = self::FILENAME): ConnectorManifest
    {
        $reader = new ManifestReader($data, $source);
        $reader->refuseUnknownKeys(self::TOP_LEVEL_KEYS);

        $key = $reader->string('key');
        $name = $reader->string('name');
        $version = $reader->string('version');
        $sdk = $reader->string('sdk');
        $vendor = $reader->string('vendor');

        $install = $reader->object('install');
        $install->refuseUnknownKeys(['mode', 'scopes', 'fields', 'settings_fields']);
        $installMode = $install->enum('mode', InstallMode::class);
        $scopes = $install->enums('scopes', InstallationScope::class);
        $installFields = self::fields($install->objects('fields', required: false));
        $settingsFields = self::fields($install->objects('settings_fields', required: false));

        $resourceKinds = [];

        foreach ($reader->objects('resource_kinds', required: false) as $definition) {
            $kind = self::resourceKind($definition);

            if ($kind !== null) {
                $resourceKinds[] = $kind;
            }
        }

        $capabilities = $reader->enums('capabilities', Capability::class, required: false);

        $messaging = $reader->object('messaging');
        $messaging->refuseUnknownKeys(['max_length', 'buttons_per_row', 'max_buttons', 'callback_data_bytes', 'supports_underline', 'supports_spoiler', 'supports_files']);
        $limits = [
            $messaging->int('max_length'),
            $messaging->int('buttons_per_row'),
            $messaging->int('max_buttons'),
            $messaging->int('callback_data_bytes'),
            $messaging->bool('supports_underline'),
            $messaging->bool('supports_spoiler'),
            $messaging->bool('supports_files'),
        ];

        $pacing = $reader->object('pacing');
        $pacing->refuseUnknownKeys(['min_interval_microseconds', 'burst', 'per_recipient_interval_microseconds']);
        $intervals = [
            $pacing->int('min_interval_microseconds'),
            $pacing->int('burst'),
            $pacing->int('per_recipient_interval_microseconds'),
        ];

        $managementCommands = $reader->enums('management_commands', ManagementCommand::class, required: false);
        $relayModes = $reader->strings('relay_modes', required: false);

        $recovery = $reader->object('recovery', required: false);
        $recovery->refuseUnknownKeys(['probes', 'standby_installations', 'resource_standby', 'mirror', 'identity_relink']);
        $facets = [
            $recovery->optionalBool('probes') ?? false,
            $recovery->optionalBool('standby_installations') ?? false,
            $recovery->optionalBool('resource_standby') ?? false,
            $recovery->optionalBool('mirror') ?? false,
            $recovery->optionalBool('identity_relink') ?? false,
        ];

        $listing = $reader->object('listing');
        $listing->refuseUnknownKeys(['category', 'tagline', 'overview', 'screenshots', 'links', 'added_at', 'changelog_url', 'sign_in_required', 'marketing']);
        $category = $listing->enum('category', ListingCategory::class);
        $tagline = $listing->string('tagline');
        $overview = $listing->string('overview');
        $screenshots = $listing->strings('screenshots', required: false);
        $addedAt = $listing->date('added_at');
        $changelogUrl = $listing->optionalString('changelog_url');
        $signInRequired = $listing->optionalBool('sign_in_required') ?? true;

        $links = $listing->object('links', required: false);
        $links->refuseUnknownKeys(['documentation', 'support', 'privacy', 'terms', 'homepage']);
        $linkValues = [
            $links->optionalString('documentation'),
            $links->optionalString('support'),
            $links->optionalString('privacy'),
            $links->optionalString('terms'),
            $links->optionalString('homepage'),
        ];

        $marketingValues = null;

        if ($listing->has('marketing')) {
            $marketing = $listing->object('marketing');
            $marketing->refuseUnknownKeys(['audience', 'place', 'places', 'installation', 'identity', 'native_payment']);
            $marketingValues = [
                $marketing->string('audience'),
                $marketing->string('place'),
                $marketing->string('places'),
                $marketing->string('installation'),
                $marketing->string('identity'),
                $marketing->optionalString('native_payment'),
            ];
        }

        $reader->throwIfInvalid($key === '' ? 'unknown' : $key);

        try {
            return new ConnectorManifest(
                key: $key,
                name: $name,
                version: $version,
                sdk: $sdk,
                vendor: $vendor,
                installMode: $installMode,
                scopes: $scopes,
                resourceKinds: $resourceKinds,
                capabilities: $capabilities,
                messaging: new MessagingLimits(...$limits),
                pacing: new Pacing(...$intervals),
                managementCommands: $managementCommands,
                relayModes: $relayModes,
                recovery: new RecoveryCapabilities(...$facets),
                listing: new Listing($category, $tagline, $overview, $screenshots, new ListingLinks(...$linkValues), $addedAt, $changelogUrl, $signInRequired, $marketingValues === null ? null : new ListingMarketing(...$marketingValues)),
                installFields: $installFields,
                settingsFields: $settingsFields,
            );
        } catch (InvalidManifest $exception) {
            throw InvalidManifest::because($key, sprintf('%s: %s', $source, $exception->reason));
        }
    }

    /**
     * @param   string                  $value  A `YYYY-MM-DD` string.
     * @return  DateTimeImmutable|null  The date, or null when it is not one.
     */
    public static function date(string $value): ?DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $date instanceof DateTimeImmutable && $date->format('Y-m-d') === $value ? $date : null;
    }

    /**
     * @param   list<ManifestReader>  $entries  The entries of `install.fields` or `install.settings_fields`.
     * @return  list<Field>           The fields that read cleanly; the others recorded their problems.
     */
    private static function fields(array $entries): array
    {
        $fields = [];

        foreach ($entries as $entry) {
            $field = self::field($entry);

            if ($field !== null) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * @param   ManifestReader  $reader  One entry of `install.fields` or `install.settings_fields`.
     * @return  Field|null      The field, or null when the entry broke a rule the reader recorded.
     */
    private static function field(ManifestReader $reader): ?Field
    {
        $before = $reader->problemCount();

        $reader->refuseUnknownKeys(['name', 'type', 'label', 'help', 'required', 'rules', 'options', 'steps', 'links']);

        $name = $reader->string('name');
        $type = $reader->enum('type', FieldType::class);
        $label = $reader->string('label');
        $help = $reader->optionalString('help');
        $required = $reader->optionalBool('required') ?? false;
        $rules = $reader->strings('rules', required: false);
        $options = $reader->stringMap('options');
        $steps = $reader->strings('steps', required: false);
        $links = $reader->stringMap('links');

        if ($reader->problemCount() > $before) {
            return null;
        }

        try {
            return new Field($name, $type, $label, $help, $required, $rules, $options, steps: $steps, links: $links);
        } catch (InvalidManifest $exception) {
            $reader->fail($exception->reason);

            return null;
        }
    }

    /**
     * @param   ManifestReader               $reader  One entry of `resource_kinds`.
     * @return  ResourceKindDefinition|null  The kind, or null when the entry broke a rule the reader recorded.
     */
    private static function resourceKind(ManifestReader $reader): ?ResourceKindDefinition
    {
        $before = $reader->problemCount();

        $reader->refuseUnknownKeys(['kind', 'label', 'portal_label', 'icon', 'grant_mode', 'supports_early_admission_hold', 'mirrorable']);

        $kind = $reader->string('kind');
        $label = $reader->string('label');
        $portalLabel = $reader->string('portal_label');
        $icon = $reader->string('icon');
        $grantMode = $reader->enum('grant_mode', GrantMode::class);
        $hold = $reader->optionalBool('supports_early_admission_hold') ?? false;
        $mirrorable = $reader->optionalBool('mirrorable') ?? false;

        if ($reader->problemCount() > $before) {
            return null;
        }

        try {
            return new ResourceKindDefinition($kind, $label, $portalLabel, $icon, $grantMode, $hold, $mirrorable);
        } catch (InvalidManifest $exception) {
            $reader->fail($exception->reason);

            return null;
        }
    }
}
