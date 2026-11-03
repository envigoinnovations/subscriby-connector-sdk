<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One audience a broadcast may address, with its size now and what it needs.
 *
 * The count is the members the audience reaches at this moment, so a surface
 * shows it beside the label; the flags say what else a draft must name
 * before the core will size it.
 */
final readonly class BroadcastAudienceOption
{
    /**
     * @param  string  $value               The audience's value, what `BroadcastDraft::$audience` takes.
     * @param  string  $label               What a creator reads, in the core's words.
     * @param  int     $count               How many members it reaches now; 0 when a pass window is needed to say.
     * @param  bool    $acceptsPlan         Whether a plan may narrow it.
     * @param  bool    $requiresPassWindow  Whether it needs a pass window to be sized or sent.
     * @param  bool    $requiresHorizon     Whether it looks a number of days ahead.
     */
    public function __construct(
        public string $value,
        public string $label,
        public int $count,
        public bool $acceptsPlan,
        public bool $requiresPassWindow,
        public bool $requiresHorizon,
    ) {}
}
