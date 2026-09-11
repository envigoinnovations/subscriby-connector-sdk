<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\ManagementCommand;
use Subscriby\Connector\Enums\MemberCommand;
use Subscriby\Connector\Enums\MessageActionKind;
use Subscriby\Connector\Exceptions\InvalidMessage;

/**
 * A button on a message.
 *
 * Built through the four factories only, so the set of things a button can do
 * stays closed. `assertWithin()` is the validating constructor the 64-byte
 * callback budget used to be tribal knowledge about: a callback that a
 * platform would truncate throws here, on the developer's machine. A command
 * button names a catalogue command and its parameters in the core's words;
 * each connector renders it into the callback its own surface answers to, so
 * no core message spells a platform's routing.
 */
final readonly class MessageAction
{
    /**
     * @param  MessageActionKind                     $kind     What the button does.
     * @param  string                                $label    What it says.
     * @param  string                                $value    The URL to open, the callback data to send, the text to copy, or the command's value.
     * @param  ManagementCommand|MemberCommand|null  $command  The catalogue command a command button runs.
     * @param  array<string, scalar>                 $params   The command's parameters, named in the core's words.
     *
     * @throws  InvalidMessage  When the label or value is empty.
     */
    private function __construct(
        public MessageActionKind $kind,
        public string $label,
        public string $value,
        public ManagementCommand|MemberCommand|null $command = null,
        public array $params = [],
    ) {
        if (trim($label) === '') {
            throw InvalidMessage::because('a button needs a label');
        }

        if ($value === '') {
            throw InvalidMessage::because(sprintf('the %s button "%s" has no value', $kind->value, $label));
        }
    }

    /**
     * @param   string  $label  What the button says.
     * @param   string  $url    Where it opens.
     * @return  self    The button.
     */
    public static function url(string $label, string $url): self
    {
        return new self(MessageActionKind::Url, $label, $url);
    }

    /**
     * @param   string  $label  What the button says.
     * @param   string  $data   The callback data the connector routes back to a handler.
     * @return  self    The button.
     */
    public static function callback(string $label, string $data): self
    {
        return new self(MessageActionKind::Callback, $label, $data);
    }

    /**
     * @param   string  $label  What the button says.
     * @param   string  $text   The text copied to the clipboard.
     * @return  self    The button.
     */
    public static function copy(string $label, string $text): self
    {
        return new self(MessageActionKind::Copy, $label, $text);
    }

    /**
     * A button that runs a catalogue command in the connector's own surface.
     *
     * @param   string                           $label    What the button says.
     * @param   ManagementCommand|MemberCommand  $command  The command it runs.
     * @param   array<string, scalar>            $params   Its parameters, named in the core's words (`conversation`, `project`).
     * @return  self                             The button.
     */
    public static function command(string $label, ManagementCommand|MemberCommand $command, array $params = []): self
    {
        return new self(MessageActionKind::Command, $label, $command->value, $command, $params);
    }

    /**
     * Refuse a button a connector cannot carry.
     *
     * @param  MessagingLimits  $limits  The connector's declared limits.
     *
     * @throws  InvalidMessage  When callback data exceeds the connector's byte budget.
     */
    public function assertWithin(MessagingLimits $limits): void
    {
        if ($this->kind === MessageActionKind::Callback && strlen($this->value) > $limits->callbackDataBytes) {
            throw InvalidMessage::because(sprintf(
                'callback data for "%s" is %d bytes, the connector allows %d',
                $this->label,
                strlen($this->value),
                $limits->callbackDataBytes,
            ));
        }
    }
}
