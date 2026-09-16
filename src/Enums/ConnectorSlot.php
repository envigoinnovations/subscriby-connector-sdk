<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Every place in the core UI a connector may contribute a view or a component.
 *
 * The core renders each slot once per installed connector and nothing when a
 * connector fills no contribution, which is how the recovery pages, the
 * resources screen and the portal stop knowing what a Telegram bot is. A slot
 * is a name the core owns; what fills it is the connector's, read from its
 * `UiSlots` port. A place the core renders from data a connector already
 * gives it (the portal's sign-in buttons from `PortalLoginMethod::button()`,
 * the portal's call to action from the manifest, the onboarding carousel
 * from the listing) is not a slot: Blade is for what data cannot carry.
 */
enum ConnectorSlot: string
{
    use EnumHelpers;

    case Install = 'install';
    case Settings = 'settings';
    case ResourceBadge = 'resource_badge';
    case ResourceLinkInstructions = 'resource_link_instructions';
    case ResourceSwap = 'resource_swap';
    case ResourceHealthDetail = 'resource_health_detail';
    case MemberIdentityBadge = 'member_identity_badge';
    case GrantAction = 'grant_action';
    case CreatorLoginMethod = 'creator_login_method';
    case PortalGrantAction = 'portal_grant_action';
    case PaymentSetup = 'payment_setup';
    case CheckoutMethod = 'checkout_method';
    case AccessCodeRedemptionHint = 'access_code_redemption_hint';
    case BroadcastHints = 'broadcast_hints';
    case SupportRelayOptions = 'support_relay_options';
    case RecoveryActions = 'recovery_actions';
    case RecoveryPrevention = 'recovery_prevention';
    case RecoverySteps = 'recovery_steps';
}
