<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use PHPUnit\Framework\Assert;
use Subscriby\Connector\Contracts\Ports\Announcer;
use Subscriby\Connector\Data\Announcement;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\DeliveryFailure;
use Subscriby\Connector\Data\DeliveryResult;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Enums\DeliveryFailureKind;

/**
 * An announcer that keeps every post, and refuses when the installation's handle says so.
 *
 * An installation whose handle is `no-announcements` has nowhere to post,
 * which stands for a server where the creator picked no channel, so the
 * core's fallback to per-member copies can be driven without a platform.
 */
final class FakeAnnouncer implements Announcer
{
    /** @var list<array{installation: string, announcement: Announcement}> Every post, oldest first. */
    public array $posted = [];

    private int $sequence = 0;

    /**
     * @param   InstallationRef  $installation  The installation.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   Announcement     $announcement  The message and its audience.
     * @return  DeliveryResult   Delivered with a sequential id, or a configuration failure for an installation with nowhere to post.
     */
    public function announce(InstallationRef $installation, CredentialBag $credentials, Announcement $announcement): DeliveryResult
    {
        if ($installation->handle === 'no-announcements') {
            return DeliveryResult::failed(new DeliveryFailure(DeliveryFailureKind::Configuration, 'The fake installation has no announcement channel.'));
        }

        $this->posted[] = ['installation' => $installation->id, 'announcement' => $announcement];

        return DeliveryResult::delivered('fake-announcement-'.++$this->sequence);
    }

    /**
     * @param  string       $installationId  The installation that should have posted.
     * @param  string|null  $containing      Text the body should contain, or null for any post.
     */
    public function assertPostedBy(string $installationId, ?string $containing = null): void
    {
        $matches = array_filter(
            $this->posted,
            fn (array $entry): bool => $entry['installation'] === $installationId && ($containing === null || str_contains($entry['announcement']->message->body, $containing)),
        );

        Assert::assertNotEmpty($matches, sprintf('Expected an announcement by installation %s%s.', $installationId, $containing === null ? '' : ' containing "'.$containing.'"'));
    }

    /**
     * Fail when anything was posted at all.
     */
    public function assertNothingPosted(): void
    {
        Assert::assertSame([], $this->posted, 'Expected no announcement, but '.count($this->posted).' were posted.');
    }
}
