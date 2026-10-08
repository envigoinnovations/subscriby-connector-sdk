<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\AffiliateSummary;
use Subscriby\Connector\Data\IdentityRecord;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\ReferralCapture;
use Subscriby\Connector\Data\ReferralLink;
use Subscriby\Connector\Data\ReferralProgramSummary;
use Subscriby\Connector\Enums\ReferralSource;
use Subscriby\Connector\Exceptions\ReferralRefused;

/**
 * A project's Referral Program, as a connector's member surface offers it.
 *
 * The programme is the core's: who may join, what each side earns, the
 * attribution window, the holds and the payouts all live there. A connector
 * is a door: it shows a member the offer and their standing, enrols them
 * when they ask, and reports the two ways a referred friend arrives through
 * it, a deep link carrying the affiliate's code and a code typed at the
 * checkout. Every call names the project through the installation the
 * conversation runs on and the member through the account that is talking,
 * the way the support inbox is fed, so a connector resolves nothing itself
 * and a member who has never spoken to the project before is created as a
 * lead on the way in.
 */
interface Referrals
{
    /**
     * The programme a member may join and share right now, or nothing.
     *
     * Live means switched on by the creator and still paid for; a paused or
     * lapsed programme answers null, so a connector shows no offer, though an
     * affiliate it already enrolled still reads their standing through
     * {@see affiliateFor()}.
     *
     * @param   InstallationRef              $installation  The project's installation the conversation runs on.
     * @return  ReferralProgramSummary|null  The live programme in words, or null for a project running none, or an installation that names no project.
     */
    public function programFor(InstallationRef $installation): ?ReferralProgramSummary;

    /**
     * Where a member stands in the project's programme, live or not.
     *
     * @param   InstallationRef        $installation  The project's installation the conversation runs on.
     * @param   IdentityRecord         $member        The account that is talking, as the platform describes it.
     * @return  AffiliateSummary|null  Their standing with their tallies, or null when they never joined or the project runs no programme.
     */
    public function affiliateFor(InstallationRef $installation, IdentityRecord $member): ?AffiliateSummary;

    /**
     * Enrol the member who asked to join.
     *
     * The core applies the programme's switches: a customers-only programme
     * refuses a member with no active membership, and an approval programme
     * enrols them pending, told to the creator, until the creator approves.
     *
     * @param   InstallationRef   $installation  The project's installation the conversation runs on.
     * @param   IdentityRecord    $member        The account that asked.
     * @return  AffiliateSummary  Their new standing, approved or awaiting approval.
     *
     * @throws  ReferralRefused  When the installation names no project, no programme is live, they already joined, or the programme is for customers and they hold no membership.
     */
    public function join(InstallationRef $installation, IdentityRecord $member): AffiliateSummary;

    /**
     * Record that a member arrived through an affiliate's link or typed their code.
     *
     * First touch wins and lasts the attribution window; the first settled
     * payment inside it converts the touch and pays both sides. The core
     * refuses a member who was referred before or has ever paid, an
     * affiliate's own code, and a code that is unknown, pending or
     * suspended, one reason for all three so a stranger learns nothing about
     * a member's standing.
     *
     * @param   InstallationRef  $installation  The project's installation the conversation runs on; its connector is recorded as the door.
     * @param   IdentityRecord   $member        The account that arrived.
     * @param   string           $code          The affiliate's code, as the link carried it or the member typed it.
     * @param   ReferralSource   $source        Which of the connector's two doors they came through.
     * @return  ReferralCapture  The touch, with what the member is promised.
     *
     * @throws  ReferralRefused  When the installation names no project, or a capture rule refused the touch.
     */
    public function capture(InstallationRef $installation, IdentityRecord $member, string $code, ReferralSource $source): ReferralCapture;

    /**
     * The addresses an affiliate hands out: the connector's deep link and the portal's.
     *
     * Empty until the member is an approved affiliate on a live programme, so
     * a connector never hands out a link that attributes nothing.
     *
     * @param   InstallationRef     $installation  The project's installation the conversation runs on.
     * @param   IdentityRecord      $member        The account that is talking.
     * @return  list<ReferralLink>  The links in the order the dashboard lists them, each labelled by where it lands.
     */
    public function links(InstallationRef $installation, IdentityRecord $member): array;

    /**
     * Record where an affiliate wants to be paid, in answer to the creator's question.
     *
     * One free text per affiliate, at most 500 characters, stored encrypted
     * by the core and read by the creator alone when they pay; the question
     * is the programme's `payoutDetailsLabel`, and a connector offers this
     * only on a cash programme that asks one.
     *
     * @param   InstallationRef   $installation  The project's installation the conversation runs on.
     * @param   IdentityRecord    $member        The account that is talking.
     * @param   string|null       $details       What they typed, or null to clear it.
     * @return  AffiliateSummary  Their standing, with the details as written.
     *
     * @throws  ReferralRefused  When the installation names no project, the member never joined (`not_joined`), or the text is too long (`invalid_details`).
     */
    public function updatePayoutDetails(InstallationRef $installation, IdentityRecord $member, ?string $details): AffiliateSummary;
}
