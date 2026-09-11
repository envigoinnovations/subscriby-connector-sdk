<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * The closed set of things a button on a message can do.
 *
 * Four shapes and no more: open a URL, send a callback the connector routes
 * to a handler, copy a value, or run a catalogue command the connector renders
 * into its own callback. Anything only one platform can do travels in the
 * message's `meta` for that connector and is ignored by every other, which is
 * what stops `Message` growing into a platform-specific DSL.
 */
enum MessageActionKind: string
{
    use EnumHelpers;

    case Url = 'url';
    case Callback = 'callback';
    case Copy = 'copy';
    case Command = 'command';
}
