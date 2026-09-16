<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * Where on the connector a creator wrote their answer to a support thread.
 *
 * The core keeps it on the message so the inbox can say how the answer
 * arrived: typed into the private conversation the relay pinged the creator
 * in, or written inside the thread the relay opened in the project's relay
 * space. Which platform it was is the thread's connector.
 */
enum SupportReplySource: string
{
    use EnumHelpers;

    case DirectMessage = 'direct_message';
    case RelaySpace = 'relay_space';
}
