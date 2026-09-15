<?php

declare(strict_types=1);

namespace Subscriby\Connector\Registry;

use Illuminate\Contracts\Translation\Translator;
use Subscriby\Connector\Contracts\Connector;
use Subscriby\Connector\Contracts\Ports\SettingsSchema;
use Subscriby\Connector\Data\ConnectorManifest;
use Subscriby\Connector\Enums\Capability;
use Subscriby\Connector\Exceptions\InvalidManifest;
use Subscriby\Connector\Manifest\ManifestSettingsSchema;
use Subscriby\Connector\Sdk;

/**
 * The agreement between a connector's manifest and the ports it binds, checked once at registration.
 *
 * Every registry runs the same checks: the application's at boot and the
 * kit's `TestRegistry` in a package's own suite, so a connector a test refuses
 * is one production would refuse, for the same reason and in the same words.
 * A required port missing, a capability declared without its port, a port
 * bound without the capability that needs it, or the money path claimed by a
 * connector that is not official are each refused at once, on the developer's
 * machine, rather than at the first call in a creator's directory.
 */
final class PortAgreement
{
    /**
     * Collect what a connector binds and check it against its manifest.
     *
     * A manifest that declares install or settings fields and binds no
     * `SettingsSchema` gets the SDK's manifest-backed schema, so a package with
     * a plain form writes no PHP for it; one that does both is refused, because
     * two sources for one form can only drift apart.
     *
     * @param   ConnectorManifest            $manifest    What the connector is, as its `connector.json` declares it.
     * @param   Connector                    $connector   The connector package's entry point.
     * @param   Translator                   $translator  Translates the labels of a manifest-declared form at read time.
     * @param   bool                         $official    Whether configuration names the connector official.
     * @return  array<class-string, object>  Every port keyed by contract, the settings form included.
     *
     * @throws  InvalidManifest  When fields are both declared and bound, or the ports and the manifest disagree.
     */
    public static function collect(ConnectorManifest $manifest, Connector $connector, Translator $translator, bool $official): array
    {
        $registrar = new PortRegistrar;
        $connector->register($registrar);
        $ports = $registrar->ports();

        if (isset($ports[SettingsSchema::class]) && $manifest->declaresFields()) {
            throw InvalidManifest::because($manifest->key, 'declares install or settings fields in connector.json and binds its own SettingsSchema; keep one of the two');
        }

        $ports[SettingsSchema::class] ??= new ManifestSettingsSchema($manifest, $translator);

        self::assert($manifest, $ports, $official);

        return $ports;
    }

    /**
     * Refuse a connector whose ports and manifest disagree.
     *
     * @param  ConnectorManifest            $manifest  The manifest.
     * @param  array<class-string, object>  $ports     What the connector bound, the settings form included.
     * @param  bool                         $official  Whether configuration names the connector official.
     *
     * @throws  InvalidManifest  Naming the first disagreement found.
     */
    public static function assert(ConnectorManifest $manifest, array $ports, bool $official): void
    {
        foreach (Sdk::REQUIRED_PORTS as $required) {
            if (! isset($ports[$required])) {
                throw InvalidManifest::because($manifest->key, sprintf('every connector must bind %s', $required));
            }
        }

        foreach ($manifest->capabilities as $capability) {
            if (! isset($ports[$capability->port()])) {
                throw InvalidManifest::because($manifest->key, sprintf('declares %s but binds no %s', $capability->value, $capability->port()));
            }

            if ($capability->requiresOfficial() && ! $official) {
                throw InvalidManifest::because($manifest->key, sprintf('%s is reserved for official connectors', $capability->value));
            }
        }

        $declaredPorts = array_map(fn (Capability $capability): string => $capability->port(), $manifest->capabilities);

        $capabilityPorts = array_map(fn (Capability $capability): string => $capability->port(), Capability::cases());

        foreach (array_keys($ports) as $port) {
            if (in_array($port, $capabilityPorts, true) && ! in_array($port, $declaredPorts, true)) {
                throw InvalidManifest::because($manifest->key, sprintf('binds %s without declaring a capability that needs it', $port));
            }
        }
    }
}
