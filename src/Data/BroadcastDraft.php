<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A broadcast as a creator's surface asks the core to size or send it.
 *
 * The message is canonical HTML, 1 to 4096 characters, which every
 * connector renders in its own markup; the audience is one of the values
 * `Core\Broadcasts::audiences()` lists, narrowed by a plan where the
 * audience takes one, or by a pass window where it needs one.
 */
final readonly class BroadcastDraft
{
    /** The audience that reaches every member with a linked account. */
    public const string EVERYONE = 'all';

    /**
     * @param  string       $message       The message, canonical HTML.
     * @param  string       $audience      The audience's value, `all` by default.
     * @param  string|null  $planId        The plan that narrows the audience, where the audience takes one.
     * @param  string|null  $passWindowId  The pass window, where the audience needs one.
     * @param  int|null     $horizonDays   How many days ahead an expiring-soon audience looks, 1 to 90; null for the core's default.
     */
    public function __construct(
        public string $message,
        public string $audience = self::EVERYONE,
        public ?string $planId = null,
        public ?string $passWindowId = null,
        public ?int $horizonDays = null,
    ) {}
}
