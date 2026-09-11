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
 * `state` is one of the words below, so the core can record a verdict without
 * knowing the platform; `detail` is the sentence in the connector's own terms.
 */
final readonly class SpaceAccess
{
    /** The installation controls the place. */
    public const string READY = 'ready';

    /** The place no longer exists on the platform. */
    public const string GONE = 'gone';

    /** The place exists, but the installation is no longer in it. */
    public const string NOT_MEMBER = 'not_member';

    /** The installation is in the place without the standing to act there (a plain member, a role too low). */
    public const string INSUFFICIENT_ROLE = 'insufficient_role';

    /** The installation has the standing but lacks a specific right it needs. */
    public const string MISSING_RIGHTS = 'missing_rights';

    /** The platform could not be asked, or answered nothing about the place; no verdict. */
    public const string UNKNOWN = 'unknown';

    /**
     * @param  bool    $ready              True when grants and revokes will work here.
     * @param  string  $state              One of this class's state words.
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
        return new self(true, self::READY);
    }

    /**
     * @param   string  $detail  Why nothing could be concluded.
     * @return  self    No verdict about the place.
     */
    public static function unknown(string $detail = ''): self
    {
        return new self(false, self::UNKNOWN, $detail);
    }

    /**
     * @return  bool  True when the platform said something about the place, so a caller may record it.
     */
    public function isVerdict(): bool
    {
        return $this->state !== self::UNKNOWN;
    }
}
