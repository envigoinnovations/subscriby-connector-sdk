<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\IdentityRecord;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Data\SmartLinkArrival;
use Subscriby\Connector\Data\SmartLinkDetails;
use Subscriby\Connector\Data\SmartLinkSummary;
use Subscriby\Connector\Exceptions\SmartLinkRefused;

/**
 * Smart Links as a connector sees them: a member arriving through one, and a
 * creator reading theirs.
 *
 * A Smart Link is a tracked address a creator hands out per channel; its
 * stable code rides in a connector's deep link as `via_<code>`, so a member
 * who opens the project's bot through it is credited to the link the way a
 * visitor who clicks it on the web is. The creator's side is read-only: the
 * links are made on the dashboard, and a connector's creator surface shows
 * them with their funnel and the addresses to share.
 */
interface SmartLinks
{
    /**
     * Credit a member who arrived through a connector's deep link carrying a Smart Link code.
     *
     * The core applies its own attribution rule (the last link wins while the
     * member has never paid) and records the connector as the door, so a
     * connector never decides whether the arrival counts.
     *
     * @param   InstallationRef   $installation  The project installation the member arrived on.
     * @param   IdentityRecord    $member        The account that is talking.
     * @param   string            $code          The code after the `via_` prefix.
     * @return  SmartLinkArrival  The link's name and, when the link sells one plan the project still offers, that plan.
     *
     * @throws  SmartLinkRefused  `installation_unknown` when the ref names no project, `code_unknown` when no live link of the project has the code.
     */
    public function arrive(InstallationRef $installation, IdentityRecord $member, string $code): SmartLinkArrival;

    /**
     * Every Smart Link of a project the creator may manage, newest first, live and archived alike.
     *
     * @param   CreatorRef              $creator  The creator asking.
     * @param   ProjectRef              $project  The project.
     * @return  list<SmartLinkSummary>  Each with its lifetime clicks, leads and paid members.
     *
     * @throws  SmartLinkRefused  `project_unknown` when the creator may not manage the project.
     */
    public function linksFor(CreatorRef $creator, ProjectRef $project): array;

    /**
     * One Smart Link with its funnel and the addresses to share.
     *
     * Named by the link alone, because a connector's callback data has no
     * room for the project beside it; the core finds the project behind the
     * link and checks the creator's right to it before reading anything else.
     *
     * @param   CreatorRef        $creator  The creator asking.
     * @param   string            $linkId   The link.
     * @return  SmartLinkDetails  The link in the core's words.
     *
     * @throws  SmartLinkRefused  `link_unknown` when no link has the id, `project_unknown` when the creator may not manage its project.
     */
    public function linkFor(CreatorRef $creator, string $linkId): SmartLinkDetails;
}
