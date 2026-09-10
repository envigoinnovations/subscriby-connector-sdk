<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * Whether the installation can act in a place, and if not why.
 *
 * Replaces a bare boolean so the dashboard can say what is actually wrong:
 * "the bot is not in this channel" and "the bot is an administrator but lacks
 * the invite right" need different things from the creator, and reporting one
 * as the other sends people to check settings on a bot that was never there.
 * `state` is the connector's stable machine word; `detail` is the sentence.
 */
final readonly class SpaceAccess
{
    /**
     * @param  bool    $ready              True when grants and revokes will work here.
     * @param  string  $state              A stable machine word (`ready`, `not_member`, `missing_rights`, `insufficient_role`).
     * @param  string  $detail             What a creator reads.
     * @param  bool    $creatorActionable  Whether the creator can fix it in the platform.
     */
    public function __construct(
        public bool $ready,
        public string $state,
        public string $detail = '',
        public bool $creatorActionable = false,
    ) {}

    /**
     * @return  self  A place the installation fully controls.
     */
    public static function ready(): self
    {
        return new self(true, 'ready');
    }
}
