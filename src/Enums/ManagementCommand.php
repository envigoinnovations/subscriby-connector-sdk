<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * The creator operations a connector's in-chat admin surface may offer.
 *
 * The core publishes the catalogue and the actions behind it; a connector
 * declares the subset it renders in its own idiom (Telegram wizards, Discord
 * slash commands and modals) and the directory shows the coverage. `Donate` is
 * the one connector extra: it exists for Telegram Stars and nowhere else, and
 * is listed so a connector can declare it without the core inventing a
 * general donations feature.
 */
enum ManagementCommand: string
{
    use EnumHelpers;

    case ProjectCreate = 'project_create';
    case ProjectSettings = 'project_settings';
    case ProjectDelete = 'project_delete';
    case InstallationConnect = 'installation_connect';
    case ManageConnectors = 'manage_connectors';
    case ResourceLink = 'resource_link';
    case ResourceManage = 'resource_manage';
    case PlanCreate = 'plan_create';
    case PlanManage = 'plan_manage';
    case PaymentMethodSetup = 'payment_method_setup';
    case CouponCreate = 'coupon_create';
    case AccessCodesGenerate = 'access_codes_generate';
    case Broadcast = 'broadcast';
    case SupportReply = 'support_reply';
    case SupportRelaySetup = 'support_relay_setup';
    case CreatorTasks = 'creator_tasks';
    case RecoveryStatus = 'recovery_status';
    case RecoveryRelink = 'recovery_relink';
    case RecoveryStandby = 'recovery_standby';
    case RecoveryReplaceResource = 'recovery_replace_resource';
    case AccountRecoveryEmail = 'account_recovery_email';
    case SetAlertDestination = 'set_alert_destination';
    case Donate = 'donate';

    /**
     * @return  bool  True for a command that only some connectors will ever have, so its absence is not a gap.
     */
    public function isConnectorExtra(): bool
    {
        return $this === self::Donate;
    }

    /**
     * The commands every connector with a management surface is measured against.
     *
     * @return  list<self>  The catalogue without the connector extras.
     */
    public static function core(): array
    {
        return array_values(array_filter(self::cases(), fn (self $command): bool => ! $command->isConnectorExtra()));
    }
}
