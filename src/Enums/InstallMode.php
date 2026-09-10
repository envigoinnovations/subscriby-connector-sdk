<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * How a creator connects a connector to a project.
 *
 * Telegram and Discord hand the creator a token they paste (`PasteCredential`);
 * Slack installs one shared application into a workspace through OAuth
 * (`OAuth`); a connector whose only installation belongs to the platform itself
 * (`SharedPlatform`) has nothing for a creator to connect at all.
 */
enum InstallMode: string
{
    use EnumHelpers;

    case PasteCredential = 'paste_credential';
    case OAuth = 'oauth';
    case SharedPlatform = 'shared_platform';
}
