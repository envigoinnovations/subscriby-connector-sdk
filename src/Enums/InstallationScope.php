<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Who an installation belongs to.
 *
 * A `Project` installation is a creator's own bot or app on one project. A
 * `Platform` installation is Subscriby's own presence on the connector, the
 * platform bot that signs creators in and runs the creator wizards; it belongs
 * to no project and is shared by every creator.
 */
enum InstallationScope: string
{
    use EnumHelpers;

    case Platform = 'platform';
    case Project = 'project';
}
