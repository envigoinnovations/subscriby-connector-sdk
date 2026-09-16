<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * What a support message a person wrote through a connector is.
 *
 * The kind is the message's, not the file's: a captioned photo is a `Photo`
 * whose body is the caption, and a plain text is `Text`. A platform whose
 * message carries something not listed here hands it over as a `Document`
 * with the platform's own reference in the attachment, so nothing a member
 * sends is dropped for want of a word.
 */
enum SupportMessageKind: string
{
    use EnumHelpers;

    case Text = 'text';
    case Photo = 'photo';
    case Video = 'video';
    case Audio = 'audio';
    case Voice = 'voice';
    case Document = 'document';
    case Sticker = 'sticker';
    case Animation = 'animation';
    case Location = 'location';
    case Contact = 'contact';
}
