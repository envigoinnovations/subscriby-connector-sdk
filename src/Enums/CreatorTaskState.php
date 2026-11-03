<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Which of a project's creator tasks a surface lists: the ones still waiting, the ones done, or both.
 */
enum CreatorTaskState: string
{
    use EnumHelpers;

    case Open = 'open';
    case Completed = 'completed';
    case All = 'all';
}
