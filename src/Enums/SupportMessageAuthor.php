<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Who wrote a message in a support conversation.
 *
 * The member on their platform, the creator or a teammate from the
 * dashboard or a connector, the core's own automatic replies, or the
 * Copilot answering on the creator's behalf.
 */
enum SupportMessageAuthor: string
{
    use EnumHelpers;

    case Member = 'member';
    case Creator = 'creator';
    case System = 'system';
    case Assistant = 'assistant';
}
