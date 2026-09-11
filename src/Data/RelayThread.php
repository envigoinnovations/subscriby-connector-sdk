<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The outcome of opening a thread for a support conversation in a relay space.
 *
 * A platform that threads conversations (a topic in a Telegram group, a thread
 * in a Discord channel) hands back the thread's id, which the core keeps on
 * the conversation and passes to every later post; a refusal comes back
 * classified so the core can tell "the bot may not open topics here" from a
 * network that never answered.
 */
final readonly class RelayThread
{
    /**
     * @param  string|null           $threadId  The platform's id for the thread, when one was opened.
     * @param  DeliveryFailure|null  $failure   Why none was, when it was not.
     */
    public function __construct(
        public ?string $threadId,
        public ?DeliveryFailure $failure = null,
    ) {}

    /**
     * @param   string  $threadId  The platform's id for the thread.
     * @return  self    An opened thread.
     */
    public static function opened(string $threadId): self
    {
        return new self($threadId);
    }

    /**
     * @param   DeliveryFailure  $failure  Why no thread was opened.
     * @return  self             A refused opening.
     */
    public static function failed(DeliveryFailure $failure): self
    {
        return new self(null, $failure);
    }
}
