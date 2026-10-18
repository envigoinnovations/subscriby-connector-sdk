<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use PHPUnit\Framework\Assert;
use RuntimeException;
use Subscriby\Connector\Contracts\Ports\CopilotSurface;
use Subscriby\Connector\Data\CopilotTurnAnswer;
use Subscriby\Connector\Data\CopilotTurnHandle;
use Subscriby\Connector\Data\CopilotTurnOpening;
use Subscriby\Connector\Data\CopilotTurnProgress;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\Recipient;

/**
 * A Copilot surface that keeps every display it was asked to open, repaint, finish and fail.
 *
 * An identity whose id starts with `blocked-` cannot be shown anything, as a
 * platform that refuses the chat would, so the core's refusal path can be
 * driven without a platform.
 */
final class FakeCopilotSurface implements CopilotSurface
{
    /** @var list<array{to: string, opening: CopilotTurnOpening, handle: CopilotTurnHandle}> */
    public array $opened = [];

    /** @var list<array{to: string, progress: CopilotTurnProgress}> */
    public array $progressed = [];

    /** @var list<array{to: string, answer: CopilotTurnAnswer}> */
    public array $finished = [];

    /** @var list<array{to: string, sentence: string}> */
    public array $failed = [];

    private int $sequence = 0;

    /**
     * @param   InstallationRef     $installation  The installation.
     * @param   CredentialBag       $credentials   Its secrets.
     * @param   Recipient           $recipient     The creator's account.
     * @param   CopilotTurnOpening  $opening       What to show first.
     * @return  CopilotTurnHandle   A handle numbered in sequence.
     *
     * @throws  RuntimeException  For a `blocked-` identity, as a platform that refuses the chat would.
     */
    public function begin(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, CopilotTurnOpening $opening): CopilotTurnHandle
    {
        if (str_starts_with($recipient->identity->externalId, 'blocked-')) {
            throw new RuntimeException('The creator blocked the fake bot.');
        }

        $handle = new CopilotTurnHandle(['display' => 'fake-display-'.++$this->sequence]);

        $this->opened[] = ['to' => $recipient->identity->externalId, 'opening' => $opening, 'handle' => $handle];

        return $handle;
    }

    /**
     * @param   InstallationRef      $installation  The installation.
     * @param   CredentialBag        $credentials   Its secrets.
     * @param   Recipient            $recipient     The creator's account.
     * @param   CopilotTurnHandle    $handle        The display.
     * @param   CopilotTurnProgress  $progress      What the turn has done so far.
     * @return  CopilotTurnHandle    The same handle.
     */
    public function progress(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, CopilotTurnHandle $handle, CopilotTurnProgress $progress): CopilotTurnHandle
    {
        $this->progressed[] = ['to' => $recipient->identity->externalId, 'progress' => $progress];

        return $handle;
    }

    /**
     * @param  InstallationRef    $installation  The installation.
     * @param  CredentialBag      $credentials   Its secrets.
     * @param  Recipient          $recipient     The creator's account.
     * @param  CopilotTurnHandle  $handle        The display.
     * @param  CopilotTurnAnswer  $answer        The finished answer.
     */
    public function finish(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, CopilotTurnHandle $handle, CopilotTurnAnswer $answer): void
    {
        $this->finished[] = ['to' => $recipient->identity->externalId, 'answer' => $answer];
    }

    /**
     * @param  InstallationRef    $installation  The installation.
     * @param  CredentialBag      $credentials   Its secrets.
     * @param  Recipient          $recipient     The creator's account.
     * @param  CopilotTurnHandle  $handle        The display.
     * @param  string             $sentence      Why there is no answer.
     */
    public function fail(InstallationRef $installation, CredentialBag $credentials, Recipient $recipient, CopilotTurnHandle $handle, string $sentence): void
    {
        $this->failed[] = ['to' => $recipient->identity->externalId, 'sentence' => $sentence];
    }

    /**
     * @param  string  $externalId  The account a display should have been opened for.
     */
    public function assertOpenedFor(string $externalId): void
    {
        $matches = array_filter($this->opened, fn (array $entry): bool => $entry['to'] === $externalId);

        Assert::assertNotEmpty($matches, sprintf('Expected a Copilot display to be opened for %s, none was.', $externalId));
    }

    /**
     * @param  string       $externalId  The account an answer should have reached.
     * @param  string|null  $containing  Text the answer's Markdown should contain, or null for any answer.
     */
    public function assertFinishedFor(string $externalId, ?string $containing = null): void
    {
        $matches = array_filter(
            $this->finished,
            fn (array $entry): bool => $entry['to'] === $externalId && ($containing === null || str_contains($entry['answer']->markdown, $containing)),
        );

        Assert::assertNotEmpty($matches, sprintf('Expected a Copilot answer for %s%s, none was finished.', $externalId, $containing === null ? '' : ' containing "'.$containing.'"'));
    }

    /**
     * Fail when any display was opened.
     */
    public function assertNothingOpened(): void
    {
        Assert::assertSame([], $this->opened, sprintf('Expected no Copilot display, %d were opened.', count($this->opened)));
    }
}
