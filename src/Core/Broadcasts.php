<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\BroadcastAudienceOption;
use Subscriby\Connector\Data\BroadcastDraft;
use Subscriby\Connector\Data\BroadcastReach;
use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Exceptions\BroadcastRefused;

/**
 * A project's broadcasts, as a connector's creator surface sizes and sends them.
 *
 * A broadcast is one message to a segment of the project's members,
 * delivered by the core through every live installation: posted once
 * where a connector announces (the `Announcer` port) and sent per member
 * everywhere else, at the pace each platform allows. The connector only
 * asks and confirms: it lists the audiences with their sizes, previews the
 * reach of a draft, and sends it once the creator has read the exact text
 * and the count, because a broadcast cannot be recalled. Every call takes
 * the `CreatorRef` of the account talking and runs as that creator, and
 * every refusal reaches the connector as `BroadcastRefused` with a stable
 * reason.
 */
interface Broadcasts
{
    /**
     * @param   CreatorRef                     $creator  The creator talking.
     * @param   ProjectRef                     $project  The project.
     * @return  list<BroadcastAudienceOption>  Every audience the project may address, with its size now and what it needs.
     *
     * @throws  BroadcastRefused  When the creator or the project is unknown.
     */
    public function audiences(CreatorRef $creator, ProjectRef $project): array;

    /**
     * @param   CreatorRef      $creator  The creator talking.
     * @param   ProjectRef      $project  The project.
     * @param   BroadcastDraft  $draft    The message and the audience.
     * @return  BroadcastReach  How many members the draft reaches and how long the send takes.
     *
     * @throws  BroadcastRefused  When the creator or the project is unknown, or the draft is refused.
     */
    public function preview(CreatorRef $creator, ProjectRef $project, BroadcastDraft $draft): BroadcastReach;

    /**
     * Queue the broadcast; it cannot be recalled once queued.
     *
     * @param   CreatorRef      $creator  The creator talking.
     * @param   ProjectRef      $project  The project.
     * @param   BroadcastDraft  $draft    The message and the audience, as the creator confirmed them.
     * @return  BroadcastReach  How many members it reaches and how long the send takes.
     *
     * @throws  BroadcastRefused  When the creator or the project is unknown, the project runs no connection, or the draft is refused.
     */
    public function send(CreatorRef $creator, ProjectRef $project, BroadcastDraft $draft): BroadcastReach;
}
