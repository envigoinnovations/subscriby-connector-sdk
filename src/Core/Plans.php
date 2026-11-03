<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\PlanDetails;
use Subscriby\Connector\Data\PlanDraft;
use Subscriby\Connector\Data\PlanOptions;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Exceptions\PlanRefused;

/**
 * A project's plans, as a connector's creator surface lists, creates, edits, publishes and deletes them.
 *
 * Every call takes the `CreatorRef` of the account talking and runs as that
 * creator with the dashboard's own rules: the name unique in the project,
 * the price at or above the floor, the currency one an active payment
 * method supports, a free plan only on a tier that allows it. The draft
 * names a subscription plan, paid once or renewing on a cycle; passes and
 * seasons keep their dated schedules on the dashboard. Every refusal
 * reaches the connector as `PlanRefused` with a stable reason.
 */
interface Plans
{
    /**
     * What a creator picks from before a plan is drafted: the currencies, the places and the cycles.
     *
     * @param   CreatorRef   $creator  The creator talking.
     * @param   ProjectRef   $project  The project.
     * @return  PlanOptions  The choices, each by the value the draft takes and the words a screen shows.
     *
     * @throws  PlanRefused  When the creator or the project is unknown.
     */
    public function options(CreatorRef $creator, ProjectRef $project): PlanOptions;

    /**
     * @param   CreatorRef         $creator  The creator talking.
     * @param   ProjectRef         $project  The project.
     * @return  list<PlanDetails>  The project's plans in storefront order, drafts included.
     *
     * @throws  PlanRefused  When the creator or the project is unknown.
     */
    public function listForProject(CreatorRef $creator, ProjectRef $project): array;

    /**
     * @param   CreatorRef        $creator  The creator talking.
     * @param   string            $planId   The plan.
     * @return  PlanDetails|null  The plan, or null when no plan the creator may manage has the id.
     *
     * @throws  PlanRefused  When the ref names no creator.
     */
    public function find(CreatorRef $creator, string $planId): ?PlanDetails;

    /**
     * @param   CreatorRef   $creator  The creator talking.
     * @param   ProjectRef   $project  The project.
     * @param   PlanDraft    $draft    The plan: name, price, currency, cycle and the places it grants are required.
     * @return  PlanDetails  The plan as written.
     *
     * @throws  PlanRefused  When the creator or the project is unknown, a field is refused, or the tier does not allow it.
     */
    public function create(CreatorRef $creator, ProjectRef $project, PlanDraft $draft): PlanDetails;

    /**
     * Change a plan; a field the draft never names keeps its value, and the places replace the current list when named.
     *
     * @param   CreatorRef   $creator  The creator talking.
     * @param   string       $planId   The plan.
     * @param   PlanDraft    $draft    The fields to change.
     * @return  PlanDetails  The plan as written.
     *
     * @throws  PlanRefused  When the creator or the plan is unknown, a field is refused, or the tier does not allow it.
     */
    public function update(CreatorRef $creator, string $planId, PlanDraft $draft): PlanDetails;

    /**
     * Put a plan on sale or take it off; existing memberships keep running either way.
     *
     * @param   CreatorRef   $creator    The creator talking.
     * @param   string       $planId     The plan.
     * @param   bool         $published  True to sell it, false to stop.
     * @return  PlanDetails  The plan as written.
     *
     * @throws  PlanRefused  When the creator or the plan is unknown, or the tier does not allow a free plan on sale.
     */
    public function setPublished(CreatorRef $creator, string $planId, bool $published): PlanDetails;

    /**
     * @param  CreatorRef  $creator  The creator talking.
     * @param  string      $planId   The plan.
     *
     * @throws  PlanRefused  When the creator or the plan is unknown, or the core will not delete it.
     */
    public function delete(CreatorRef $creator, string $planId): void;
}
