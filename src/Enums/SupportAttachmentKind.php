<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * What kind of file rides along with a support reply.
 *
 * A platform sends a picture, a clip, a voice note and a document through
 * different calls and shows them differently, so the core says which it is
 * and the connector picks the call; a kind the platform has no call for is
 * sent as a plain file.
 */
enum SupportAttachmentKind: string
{
    use EnumHelpers;

    case Image = 'image';
    case Video = 'video';
    case Animation = 'animation';
    case Voice = 'voice';
    case Audio = 'audio';
    case File = 'file';
}
