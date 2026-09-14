<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Contracts\Ports\AccessController;
use Subscriby\Connector\Contracts\Ports\ManagementSurface;
use Subscriby\Connector\Contracts\Ports\Messenger;
use Subscriby\Connector\Contracts\Ports\PortalLoginMethod;
use Subscriby\Connector\Contracts\Ports\ProvidesPaymentMethods;
use Subscriby\Connector\Contracts\Ports\RecoverySupport;
use Subscriby\Connector\Contracts\Ports\RegistersCreators;
use Subscriby\Connector\Contracts\Ports\SupportRelay;
use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * What a connector declares it can do, and which port proves it.
 *
 * A manifest lists capabilities and the connector binds ports; the two must
 * agree, and the capability matrix test holds them to it in both directions.
 * The core reads capabilities to decide what to render and what to ask a
 * connector for, so a capability that is declared but not bound is a lie the
 * dashboard would show, and a port bound without its capability is a feature
 * nobody can reach.
 */
enum Capability: string
{
    use EnumHelpers;

    case Messaging = 'messaging';
    case Broadcasts = 'broadcasts';
    case AccessControl = 'access_control';
    case EarlyAdmissionHold = 'early_admission_hold';
    case SupportRelay = 'support_relay';
    case NativePayments = 'native_payments';
    case PortalLogin = 'portal_login';
    case CreatorRegistration = 'creator_registration';
    case ManagementSurface = 'management_surface';
    case RecoveryProbes = 'recovery_probes';
    case RecoveryStandbyInstallations = 'recovery_standby_installations';
    case RecoveryResourceStandby = 'recovery_resource_standby';
    case RecoveryMirror = 'recovery_mirror';
    case RecoveryIdentityRelink = 'recovery_identity_relink';

    /**
     * The port a connector must bind to honour this capability.
     *
     * Several recovery capabilities share one port, `RecoverySupport`, because
     * they are facets of one subsystem the connector either understands or
     * does not; the manifest's `recovery` block says which facets are real.
     *
     * @return  class-string  The port contract.
     */
    public function port(): string
    {
        return match ($this) {
            self::Messaging, self::Broadcasts => Messenger::class,
            self::AccessControl, self::EarlyAdmissionHold => AccessController::class,
            self::SupportRelay => SupportRelay::class,
            self::NativePayments => ProvidesPaymentMethods::class,
            self::PortalLogin => PortalLoginMethod::class,
            self::CreatorRegistration => RegistersCreators::class,
            self::ManagementSurface => ManagementSurface::class,
            self::RecoveryProbes,
            self::RecoveryStandbyInstallations,
            self::RecoveryResourceStandby,
            self::RecoveryMirror,
            self::RecoveryIdentityRelink => RecoverySupport::class,
        };
    }

    /**
     * @return  bool  True for a capability that sits on the money path and is reserved for official connectors.
     */
    public function requiresOfficial(): bool
    {
        return $this === self::NativePayments;
    }

    /**
     * The group a page shelves this capability under.
     *
     * Talking to members, letting them in, surviving a ban and taking money
     * are the four stories a connector tells; every capability belongs to
     * exactly one, so a list of fourteen switches reads as four cards.
     *
     * @return  CapabilityGroup  The shelf.
     */
    public function group(): CapabilityGroup
    {
        return match ($this) {
            self::Messaging, self::Broadcasts, self::SupportRelay => CapabilityGroup::Messaging,
            self::AccessControl,
            self::EarlyAdmissionHold,
            self::ManagementSurface,
            self::PortalLogin,
            self::CreatorRegistration => CapabilityGroup::Access,
            self::RecoveryProbes,
            self::RecoveryStandbyInstallations,
            self::RecoveryResourceStandby,
            self::RecoveryMirror,
            self::RecoveryIdentityRelink => CapabilityGroup::Recovery,
            self::NativePayments => CapabilityGroup::Payments,
        };
    }
}
