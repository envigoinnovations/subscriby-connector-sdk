<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Exceptions\InvalidMessage;

/**
 * A message the core composes once and any connector renders.
 *
 * The body is the canonical HTML subset the application has always written
 * (`<b> <i> <u> <s> <a> <code> <pre> <blockquote>`); each connector's
 * `TextRenderer` renders it down, which is what lets ten locales of existing
 * copy move to a new platform without a migration. Buttons are the closed set
 * in {@see MessageAction}. Anything only one platform can do lives under
 * `meta[connector]` and every other connector ignores it.
 */
final readonly class Message
{
    /**
     * @param  string                               $body     The canonical HTML.
     * @param  list<MessageAction>                  $actions  The buttons, in order.
     * @param  array<string, array<string, mixed>>  $meta     Per-connector extras, keyed by connector key.
     *
     * @throws  InvalidMessage  When the body is empty.
     */
    public function __construct(
        public string $body,
        public array $actions = [],
        public array $meta = [],
    ) {
        if (trim($body) === '') {
            throw InvalidMessage::because('the body is empty');
        }
    }

    /**
     * @param   string                $connector  The connector key.
     * @return  array<string, mixed>  The extras meant for that connector, empty for any other.
     */
    public function metaFor(string $connector): array
    {
        return $this->meta[$connector] ?? [];
    }

    /**
     * Refuse a message a connector cannot carry.
     *
     * @param  MessagingLimits  $limits  The connector's declared limits.
     *
     * @throws  InvalidMessage  When the body or the buttons exceed them.
     */
    public function assertWithin(MessagingLimits $limits): void
    {
        $length = mb_strlen($this->body);

        if ($length > $limits->maxLength) {
            throw InvalidMessage::because(sprintf('the body is %d characters, the connector allows %d', $length, $limits->maxLength));
        }

        if (count($this->actions) > $limits->maxButtons) {
            throw InvalidMessage::because(sprintf('%d buttons were given, the connector allows %d', count($this->actions), $limits->maxButtons));
        }

        foreach ($this->actions as $action) {
            $action->assertWithin($limits);
        }
    }
}
