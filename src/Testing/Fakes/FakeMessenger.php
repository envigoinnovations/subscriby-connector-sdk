<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use PHPUnit\Framework\Assert;
use Subscriby\Connector\Contracts\Ports\Messenger;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\DeliveryFailure;
use Subscriby\Connector\Data\DeliveryResult;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\Message;
use Subscriby\Connector\Data\Recipient;
use Subscriby\Connector\Enums\DeliveryFailureKind;

/**
 * A messenger that keeps what it was asked to send.
 *
 * An identity whose id starts with `blocked-` is unreachable and files are
 * refused as a configuration failure, matching a platform with no attachments.
 */
final class FakeMessenger implements Messenger
{
    /** @var list<array{to: string, message: Message}> */
    public array $sent = [];

    private int $sequence = 0;

    /**
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   Recipient        $recipient     Who receives it.
     * @param   Message          $message       The message.
     * @return  DeliveryResult   Delivered with a sequential id, or unreachable for a `blocked-` identity.
     */
    public function send(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, Message $message): DeliveryResult
    {
        if (str_starts_with($recipient->identity->externalId, 'blocked-')) {
            return DeliveryResult::failed(new DeliveryFailure(DeliveryFailureKind::Unreachable, 'The member blocked the fake bot.'));
        }

        $this->sent[] = ['to' => $recipient->identity->externalId, 'message' => $message];

        return DeliveryResult::delivered('fake-message-'.++$this->sequence);
    }

    /**
     * @param   InstallationRef  $installation       The installation.
     * @param   CredentialBag    $credentials        Its secrets.
     * @param   Recipient        $recipient          Where the original went.
     * @param   string           $externalMessageId  The original's id.
     * @param   Message          $message            The replacement.
     * @return  DeliveryResult   Always delivered.
     */
    public function edit(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, string $externalMessageId, Message $message): DeliveryResult
    {
        return DeliveryResult::delivered($externalMessageId);
    }

    /**
     * @param   InstallationRef  $installation       The installation.
     * @param   CredentialBag    $credentials        Its secrets.
     * @param   Recipient        $recipient          Where the original went.
     * @param   string           $externalMessageId  The original's id.
     * @return  DeliveryResult   Always delivered.
     */
    public function delete(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, string $externalMessageId): DeliveryResult
    {
        return DeliveryResult::delivered($externalMessageId);
    }

    /**
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   Recipient        $recipient     Who receives it.
     * @param   string           $url           The file.
     * @param   string|null      $caption       A caption.
     * @return  DeliveryResult   Always a configuration failure: the fake platform has no files.
     */
    public function sendFile(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, string $url, ?string $caption = null): DeliveryResult
    {
        return DeliveryResult::failed(new DeliveryFailure(DeliveryFailureKind::Configuration, 'The fake platform cannot carry files.'));
    }

    /**
     * @param  string       $externalId  The account that should have received a message.
     * @param  string|null  $containing  Text the body should contain, or null for any message.
     */
    public function assertSentTo(string $externalId, ?string $containing = null): void
    {
        $matches = array_filter(
            $this->sent,
            fn (array $entry): bool => $entry['to'] === $externalId && ($containing === null || str_contains($entry['message']->body, $containing)),
        );

        Assert::assertNotEmpty($matches, sprintf('Expected a message to %s%s, none was sent.', $externalId, $containing === null ? '' : ' containing "'.$containing.'"'));
    }

    /**
     * Fail when anything was sent.
     */
    public function assertNothingSent(): void
    {
        Assert::assertSame([], $this->sent, sprintf('Expected no messages, %d were sent.', count($this->sent)));
    }
}
