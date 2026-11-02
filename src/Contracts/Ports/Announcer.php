<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

use Subscriby\Connector\Data\Announcement;
use Subscriby\Connector\Data\CredentialBag;
use Subscriby\Connector\Data\DeliveryResult;
use Subscriby\Connector\Data\InstallationRef;

/**
 * Carrying a broadcast to a project's members as one post, in the place the platform keeps for announcements.
 *
 * Bound by connectors that declare `announcements`. Where a platform reads
 * one message to many as spam (a bot writing to every member one by one) and
 * has a channel for exactly this, the core hands the broadcast to the
 * connector once per installation and the connector posts it where the
 * creator said, mentioning the audience in its own idiom; members who asked
 * to be told directly still get the core's per-person copy through the
 * `Messenger`.
 */
interface Announcer
{
    /**
     * @param   InstallationRef  $installation  The installation posting.
     * @param   CredentialBag    $credentials   Its secrets.
     * @param   Announcement     $announcement  The message and who it is for.
     * @return  DeliveryResult   Delivered with the platform's id for the post, or the classified failure.
     */
    public function announce(InstallationRef $installation, CredentialBag $credentials, Announcement $announcement): DeliveryResult;
}
