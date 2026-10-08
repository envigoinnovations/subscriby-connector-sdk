<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\AffiliatePage;
use Subscriby\Connector\Data\AffiliateRecord;
use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\ProjectRef;
use Subscriby\Connector\Data\ReferralOption;
use Subscriby\Connector\Data\ReferralPayoutDraft;
use Subscriby\Connector\Data\ReferralPayoutRecord;
use Subscriby\Connector\Data\ReferralProgramDetails;
use Subscriby\Connector\Data\ReferralProgramDraft;
use Subscriby\Connector\Data\ReferralProgramOptions;
use Subscriby\Connector\Enums\AffiliateStatus;
use Subscriby\Connector\Exceptions\ReferralRefused;

/**
 * A project's Referral Program, as a connector's creator surface runs it.
 *
 * The member side is {@see Referrals}; this is the creator's: set the
 * programme up and change it, switch it on and off, delete it, see who
 * joined and where they stand, approve, suspend and enrol affiliates, and
 * record what was paid to them. Every call names the creator through their
 * ref and the project through its ref, and the core authorises exactly as
 * the dashboard does for the same creator on the same project, so a
 * teammate refused on the web is refused in a bot, and a project the
 * creator may not see answers as unknown. Every refusal the core words for
 * itself leaves as {@see ReferralRefused} with a stable reason: `project_unknown`,
 * `forbidden`, `not_entitled` (the owner's plan lacks the programme),
 * `program_missing`, `affiliate_unknown`, `member_unknown`, `invalid_settings`,
 * `programme_owes_balances` (a delete while commissions are owed),
 * `payout_exceeds_balance`, and the join refusals a creator meets when
 * enrolling a member by hand (`already_joined`, `customers_only`).
 */
interface ReferralManagement
{
    /**
     * What the creator may choose from and the limits the core holds, before a settings conversation starts.
     *
     * @param   CreatorRef              $creator  The creator talking.
     * @param   ProjectRef              $project  The project.
     * @return  ReferralProgramOptions  The entitlement answers, the pickable coupons, plans and currencies, and the limits.
     *
     * @throws  ReferralRefused  When the project is unknown to this creator.
     */
    public function options(CreatorRef $creator, ProjectRef $project): ReferralProgramOptions;

    /**
     * The project's programme as the creator reads it, or null before the first save.
     *
     * @param   CreatorRef                   $creator  The creator talking.
     * @param   ProjectRef                   $project  The project.
     * @return  ReferralProgramDetails|null  Every setting, the state, the words and the figures; null while none was set up.
     *
     * @throws  ReferralRefused  When the project is unknown to this creator.
     */
    public function programOf(CreatorRef $creator, ProjectRef $project): ?ReferralProgramDetails;

    /**
     * Save the programme: the first save creates it, every later save changes what the draft names.
     *
     * The core applies the dashboard's rules: the owner's plan has to
     * include the programme, a cash programme needs a type and a rate, a
     * fixed commission a currency, a friend coupon the Coupons capability and
     * one of the project's own codes; affiliates are told when a change
     * moves a term they were promised.
     *
     * @param   CreatorRef              $creator  The creator talking.
     * @param   ProjectRef              $project  The project.
     * @param   ReferralProgramDraft    $draft    The fields to write.
     * @return  ReferralProgramDetails  The programme as written.
     *
     * @throws  ReferralRefused  When the project is unknown, the creator may not change it, the plan lacks the programme, or the settings do not add up.
     */
    public function saveProgram(CreatorRef $creator, ProjectRef $project, ReferralProgramDraft $draft): ReferralProgramDetails;

    /**
     * Switch the programme on or off; off stops links attributing and payments earning while every balance stays.
     *
     * @param   CreatorRef              $creator  The creator talking.
     * @param   ProjectRef              $project  The project.
     * @param   bool                    $active   The switch position.
     * @return  ReferralProgramDetails  The programme as written.
     *
     * @throws  ReferralRefused  When the project is unknown, no programme exists, or the creator may not change it.
     */
    public function setProgramActive(CreatorRef $creator, ProjectRef $project, bool $active): ReferralProgramDetails;

    /**
     * Delete the programme with its affiliates, referrals, rewards and payouts.
     *
     * @param  CreatorRef  $creator  The creator talking.
     * @param  ProjectRef  $project  The project.
     *
     * @throws  ReferralRefused  When the project is unknown, no programme exists, the creator may not delete it, or commissions are still owed (`programme_owes_balances`).
     */
    public function deleteProgram(CreatorRef $creator, ProjectRef $project): void;

    /**
     * One page of the project's affiliates, newest first, optionally of one standing.
     *
     * @param   CreatorRef            $creator  The creator talking.
     * @param   ProjectRef            $project  The project.
     * @param   AffiliateStatus|null  $status   Only affiliates of this standing, or every one.
     * @param   int                   $page     The page, from one.
     * @param   int                   $perPage  Rows per page.
     * @return  AffiliatePage         The page, with its paging figures.
     *
     * @throws  ReferralRefused  When the project is unknown to this creator.
     */
    public function affiliates(CreatorRef $creator, ProjectRef $project, ?AffiliateStatus $status, int $page, int $perPage): AffiliatePage;

    /**
     * One affiliate of the project, with their tallies, balances and payout details.
     *
     * @param   CreatorRef            $creator      The creator talking.
     * @param   ProjectRef            $project      The project.
     * @param   string                $affiliateId  The affiliate.
     * @return  AffiliateRecord|null  The affiliate, or null when the id names none of this project's.
     *
     * @throws  ReferralRefused  When the project is unknown to this creator.
     */
    public function affiliate(CreatorRef $creator, ProjectRef $project, string $affiliateId): ?AffiliateRecord;

    /**
     * Approve a pending or suspended affiliate: their code attributes from now on, and they are handed their link.
     *
     * @param   CreatorRef       $creator      The creator talking.
     * @param   ProjectRef       $project      The project.
     * @param   string           $affiliateId  The affiliate.
     * @return  AffiliateRecord  Their new standing.
     *
     * @throws  ReferralRefused  When the project or affiliate is unknown, or the creator may not change affiliates.
     */
    public function approveAffiliate(CreatorRef $creator, ProjectRef $project, string $affiliateId): AffiliateRecord;

    /**
     * Suspend an affiliate: their code stops attributing and their referrals stop earning; what they earned stays.
     *
     * @param   CreatorRef       $creator      The creator talking.
     * @param   ProjectRef       $project      The project.
     * @param   string           $affiliateId  The affiliate.
     * @return  AffiliateRecord  Their new standing.
     *
     * @throws  ReferralRefused  When the project or affiliate is unknown, or the creator may not change affiliates.
     */
    public function suspendAffiliate(CreatorRef $creator, ProjectRef $project, string $affiliateId): AffiliateRecord;

    /**
     * The project's members whose name, email or handle matches, for enrolling one by hand.
     *
     * @param   CreatorRef            $creator  The creator talking.
     * @param   ProjectRef            $project  The project.
     * @param   string                $query    What the creator typed; fewer than two characters match nobody.
     * @param   int                   $limit    At most this many, by name.
     * @return  list<ReferralOption>  The members, each by id and name.
     *
     * @throws  ReferralRefused  When the project is unknown to this creator.
     */
    public function searchMembers(CreatorRef $creator, ProjectRef $project, string $query, int $limit): array;

    /**
     * Enrol a member the creator chose, approved on the spot, and hand them their link.
     *
     * @param   CreatorRef       $creator   The creator talking.
     * @param   ProjectRef       $project   The project.
     * @param   string           $memberId  The member's row.
     * @return  AffiliateRecord  The new affiliate.
     *
     * @throws  ReferralRefused  When the project or member is unknown, no programme exists, the creator may not enrol, or the member already joined.
     */
    public function enrolMember(CreatorRef $creator, ProjectRef $project, string $memberId): AffiliateRecord;

    /**
     * Record money the creator paid an affiliate, against their oldest payable commissions.
     *
     * @param   CreatorRef            $creator      The creator talking.
     * @param   ProjectRef            $project      The project.
     * @param   string                $affiliateId  The affiliate paid.
     * @param   ReferralPayoutDraft   $payout       What was paid, in which currency, with the creator's reference and note.
     * @return  ReferralPayoutRecord  The payout, with what it covered and what is still owed.
     *
     * @throws  ReferralRefused  When the project or affiliate is unknown, the creator may not record payouts, or the amount is nothing or more than is payable (`payout_exceeds_balance`).
     */
    public function recordPayout(CreatorRef $creator, ProjectRef $project, string $affiliateId, ReferralPayoutDraft $payout): ReferralPayoutRecord;
}
