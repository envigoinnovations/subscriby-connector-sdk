<?php

declare(strict_types=1);

namespace Subscriby\Connector\Events;

use DateTimeImmutable;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Enums\InstallationState;

/**
 * The core has written a health verdict on an installation.
 *
 * Dispatched by the core for every verdict it records, whether a probe's or
 * a recovery's, so a connector can follow the standing of an installation
 * without listening to the core's own events. Most verdicts repeat the last
 * one: `$changed` says whether this one is a transition, so a connector that
 * only cares about outages and recoveries reads that flag, while one that
 * keeps its own row in step (the Telegram connector stamps the bot row's
 * health columns on every probe) acts on all of them.
 */
final readonly class InstallationHealthRecorded
{
    /**
     * @param  InstallationRef    $installation  The installation the verdict is about.
     * @param  InstallationState  $state         Where it stands now: `Connected` when it answers, `Degraded` when the platform refused it.
     * @param  string|null        $reason        The reason code the core recorded, null when it answers.
     * @param  bool               $changed       Whether the standing differs from the one recorded before.
     * @param  DateTimeImmutable  $checkedAt     When the verdict was taken.
     */
    public function __construct(
        public InstallationRef $installation,
        public InstallationState $state,
        public ?string $reason,
        public bool $changed,
        public DateTimeImmutable $checkedAt,
    ) {}

    /**
     * @return  bool  True when the platform refused the installation.
     */
    public function isDegraded(): bool
    {
        return $this->state === InstallationState::Degraded;
    }
}
