<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Exceptions\InvalidManifest;

/**
 * What one message may carry on a connector.
 *
 * The core composes every message in one canonical shape and checks it against
 * these limits before handing it to the connector, so the 64-byte callback
 * budget that used to be tribal knowledge in a rule file is a number the
 * connector declares and a constructor enforces.
 */
final readonly class MessagingLimits
{
    /**
     * @param  int   $maxLength          Characters in a body.
     * @param  int   $buttonsPerRow      Buttons the platform lays out on one row.
     * @param  int   $maxButtons         Buttons one message may carry in total.
     * @param  int   $callbackDataBytes  Bytes of callback data one button may carry.
     * @param  bool  $supportsUnderline  Whether `<u>` survives rendering.
     * @param  bool  $supportsSpoiler    Whether a spoiler span survives rendering.
     * @param  bool  $supportsFiles      Whether files can be attached to a message.
     *
     * @throws  InvalidManifest  When a limit is not a positive number.
     */
    public function __construct(
        public int $maxLength,
        public int $buttonsPerRow,
        public int $maxButtons,
        public int $callbackDataBytes,
        public bool $supportsUnderline,
        public bool $supportsSpoiler,
        public bool $supportsFiles,
    ) {
        foreach (['maxLength' => $maxLength, 'buttonsPerRow' => $buttonsPerRow, 'maxButtons' => $maxButtons, 'callbackDataBytes' => $callbackDataBytes] as $name => $value) {
            if ($value < 1) {
                throw InvalidManifest::because('unknown', sprintf('messaging limit %s must be a positive number, %d given', $name, $value));
            }
        }
    }
}
