<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\Capability;

/**
 * Which facets of the Disaster Recovery Program a connector implements.
 *
 * The program is one core subsystem with five connector-facing facets. A
 * Telegram bot can be banned and replaced by a standby, a channel can be
 * mirrored, a creator's account can be relinked; a Discord role has none of
 * those failure modes and only its health can be probed. The recovery pages
 * render exactly the facets an installed connector declares.
 */
final readonly class RecoveryCapabilities
{
    /**
     * @param  bool  $probes                Health probes for installations, spaces and identities.
     * @param  bool  $standbyInstallations  A second installation kept ready to take over.
     * @param  bool  $resourceStandby       A standby space kept ready for a resource.
     * @param  bool  $mirror                Live copying of a resource's posts into its standby.
     * @param  bool  $identityRelink        Re-proving a creator's account after a ban, and backup identities.
     */
    public function __construct(
        public bool $probes = false,
        public bool $standbyInstallations = false,
        public bool $resourceStandby = false,
        public bool $mirror = false,
        public bool $identityRelink = false,
    ) {}

    /**
     * @return  self  A connector that takes no part in the recovery program.
     */
    public static function none(): self
    {
        return new self;
    }

    /**
     * The capabilities these flags amount to, for the manifest's declared list.
     *
     * @return  list<Capability>  In flag order.
     */
    public function capabilities(): array
    {
        $capabilities = [];

        foreach ([
            Capability::RecoveryProbes->value => $this->probes,
            Capability::RecoveryStandbyInstallations->value => $this->standbyInstallations,
            Capability::RecoveryResourceStandby->value => $this->resourceStandby,
            Capability::RecoveryMirror->value => $this->mirror,
            Capability::RecoveryIdentityRelink->value => $this->identityRelink,
        ] as $capability => $enabled) {
            if ($enabled) {
                $capabilities[] = Capability::from($capability);
            }
        }

        return $capabilities;
    }

    /**
     * @return  bool  True when any facet is implemented.
     */
    public function any(): bool
    {
        return $this->capabilities() !== [];
    }
}
