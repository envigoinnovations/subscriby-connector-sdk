<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A batch of access codes as a creator's surface asks the core to generate it.
 *
 * The codes are generated on the queue and reach the creator on their
 * account, as a file or one message per code, so the batch names how; a
 * custom expiry names its amount and unit, every other preset is the
 * core's own.
 */
final readonly class AccessCodeBatch
{
    /** The codes reach the creator as one file. */
    public const string EXPORT_FILE = 'file';

    /** The codes reach the creator as one message per code. */
    public const string EXPORT_MESSAGES = 'messages';

    /** The codes never expire. */
    public const string EXPIRY_NONE = 'no_expiry';

    /** The codes expire a day after generation. */
    public const string EXPIRY_ONE_DAY = '1_day';

    /** The codes expire a week after generation. */
    public const string EXPIRY_ONE_WEEK = '1_week';

    /** The codes expire a month after generation. */
    public const string EXPIRY_ONE_MONTH = '1_month';

    /** The codes expire a year after generation. */
    public const string EXPIRY_ONE_YEAR = '1_year';

    /** The codes expire after the amount and unit the batch names. */
    public const string EXPIRY_CUSTOM = 'custom';

    /**
     * @param  string       $planId              The plan the codes attach to.
     * @param  int          $quantity            How many codes, 1 to 1000.
     * @param  string       $exportType          `file` or `messages`.
     * @param  string       $expiryPreset        One of the expiry constants.
     * @param  int|null     $customExpiryAmount  The amount, when the preset is custom.
     * @param  string|null  $customExpiryUnit    `days`, `weeks`, `months` or `years`, when the preset is custom.
     */
    public function __construct(
        public string $planId,
        public int $quantity,
        public string $exportType = self::EXPORT_FILE,
        public string $expiryPreset = self::EXPIRY_NONE,
        public ?int $customExpiryAmount = null,
        public ?string $customExpiryUnit = null,
    ) {}
}
