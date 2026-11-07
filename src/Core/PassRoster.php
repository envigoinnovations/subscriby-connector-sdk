<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\PassHolderPage;
use Subscriby\Connector\Data\PassHolderTrail;
use Subscriby\Connector\Data\PassRosterDates;
use Subscriby\Connector\Data\PassRosterPlan;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Exceptions\PassRosterRefused;

/**
 * The Pass Roster as a connector's creator surface reads it, one date at a time.
 *
 * The dashboard draws the roster as a grid of members against dates; a chat
 * has no grid, so this port walks it the way a conversation can: the plans
 * that sell dates, a plan's coming dates, the holders of one date with where
 * each stands, and one pass's trail of moves. Every call takes the
 * `CreatorRef` of the account talking and runs as that creator under the
 * dashboard's rules, the members list's own permission, refused as
 * `PassRosterRefused` with a stable reason otherwise. The words are the
 * core's: a standing and a move reason cross as enums with the dashboard's
 * translated word beside them, and a date with its range in the plan's own
 * zone, so a surface prints what the page prints.
 */
interface PassRoster
{
    /**
     * @param   CreatorRef            $creator  The creator asking.
     * @param   ProjectRef            $project  The project.
     * @return  list<PassRosterPlan>  The plans that sell dates, passes and seasons alike, by name.
     *
     * @throws  PassRosterRefused  `creator_unknown`, `project_unknown` or `not_permitted`.
     */
    public function plansFor(CreatorRef $creator, ProjectRef $project): array;

    /**
     * @param   CreatorRef       $creator     The creator asking.
     * @param   string           $planId      A plan that sells dates.
     * @param   int              $weeksAhead  How far ahead to look.
     * @return  PassRosterDates  The plan, and its dates that have not ended and start within the weeks, soonest first, cancelled ones included and marked, each with its roll call.
     *
     * @throws  PassRosterRefused  `creator_unknown`, `plan_unknown` (no such plan the creator may manage, or one that sells no dates), `project_unknown` or `not_permitted`.
     */
    public function datesFor(CreatorRef $creator, string $planId, int $weeksAhead = 8): PassRosterDates;

    /**
     * @param   CreatorRef      $creator  The creator asking.
     * @param   string          $dateId   The date.
     * @param   int             $page     Which page of holders, one-based.
     * @param   int             $perPage  How many holders a page carries, at most a hundred.
     * @return  PassHolderPage  The date with its roll call and the page of holders in the order they bought.
     *
     * @throws  PassRosterRefused  `creator_unknown`, `date_unknown`, `project_unknown` or `not_permitted`.
     */
    public function holdersOf(CreatorRef $creator, string $dateId, int $page = 1, int $perPage = 8): PassHolderPage;

    /**
     * @param   CreatorRef       $creator  The creator asking.
     * @param   string           $passId   One holder's pass on a date, as a `PassHolder` names it.
     * @return  PassHolderTrail  The holder, the date their pass is on now, and every hop the pass made, oldest first.
     *
     * @throws  PassRosterRefused  `creator_unknown`, `pass_unknown`, `project_unknown` or `not_permitted`.
     */
    public function trailOf(CreatorRef $creator, string $passId): PassHolderTrail;
}
