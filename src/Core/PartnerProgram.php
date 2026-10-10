<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Data\PartnerAudienceOption;
use Subscriby\Connector\Data\PartnerCommitmentDraft;
use Subscriby\Connector\Data\PartnerCommitmentOptions;
use Subscriby\Connector\Data\PartnerCommitmentRecord;
use Subscriby\Connector\Data\PartnerPayoutDraft;
use Subscriby\Connector\Data\PartnerPayoutOptions;
use Subscriby\Connector\Data\PartnerPayoutPage;
use Subscriby\Connector\Data\PartnerReferralPage;
use Subscriby\Connector\Data\PartnerRewardPage;
use Subscriby\Connector\Data\PartnerSummary;
use Subscriby\Connector\Exceptions\PartnerRefused;

/**
 * The Partner Program, as a connector's creator surface shows it: creators referring creators to the platform.
 *
 * The programme is the platform's, not a project's: a creator applies once
 * from the dashboard, the owner reads the application by hand, and an
 * approved partner holds a code, shares a link, earns a share of every
 * invoice a referred creator pays, climbs a ladder of points by promotion
 * work the owner verifies, and is paid as credit or in cash. A connector
 * is a door to the same standing: it shows where the creator stands and
 * what the programme pays in the core's own words, their referrals,
 * commissions, payouts and commitments, takes the proof of a commitment and
 * a commitment added later, and collects how they want to be paid; the
 * application itself is the dashboard's. Every call names the creator
 * through their ref and the core authorises exactly as the dashboard does
 * for them. Every refusal leaves as {@see PartnerRefused} with a stable
 * reason: `creator_unknown`, `not_partner`, `not_approved`, `forbidden`,
 * `commitment_unknown`, `invalid_details`, and the core's own
 * (`terms_not_accepted`, `payout_details_missing`, `bank_account_incomplete`,
 * `credit_rail_off`, `commitment_already_verified`, `commitment_already_held`,
 * `commitment_audience_unfit`, `commitment_audience_too_small`).
 */
interface PartnerProgram
{
    /**
     * Where the creator stands, with everything a screen prints for that standing.
     *
     * @param   CreatorRef      $creator  The creator talking.
     * @return  PartnerSummary  The standing; a creator who never applied reads the pitch and the terms, an approved one their code, links, rate, tallies, balance and payout standing.
     *
     * @throws  PartnerRefused  When the ref names no creator.
     */
    public function summaryFor(CreatorRef $creator): PartnerSummary;

    /**
     * One page of the creators the partner referred, newest first.
     *
     * @param   CreatorRef           $creator  The creator talking.
     * @param   int                  $page     The page, from one.
     * @param   int                  $perPage  Rows per page.
     * @return  PartnerReferralPage  The page, with its paging figures.
     *
     * @throws  PartnerRefused  When the ref names no creator, or they were never approved.
     */
    public function referrals(CreatorRef $creator, int $page, int $perPage): PartnerReferralPage;

    /**
     * One page of the partner's commissions and clawbacks, newest first.
     *
     * @param   CreatorRef         $creator  The creator talking.
     * @param   int                $page     The page, from one.
     * @param   int                $perPage  Rows per page.
     * @return  PartnerRewardPage  The page, with its paging figures.
     *
     * @throws  PartnerRefused  When the ref names no creator, or they were never approved.
     */
    public function rewards(CreatorRef $creator, int $page, int $perPage): PartnerRewardPage;

    /**
     * One page of the cash payouts recorded for the partner, newest first.
     *
     * @param   CreatorRef         $creator  The creator talking.
     * @param   int                $page     The page, from one.
     * @param   int                $perPage  Rows per page.
     * @return  PartnerPayoutPage  The page, with its paging figures.
     *
     * @throws  PartnerRefused  When the ref names no creator, or they were never approved.
     */
    public function payouts(CreatorRef $creator, int $page, int $perPage): PartnerPayoutPage;

    /**
     * Every commitment the partner holds, in the order they were made, with what each adds to the rate today.
     *
     * @param   CreatorRef                     $creator  The creator talking.
     * @return  list<PartnerCommitmentRecord>  The commitments; empty for a partner who made none.
     *
     * @throws  PartnerRefused  When the ref names no creator, or they were never approved.
     */
    public function commitments(CreatorRef $creator): array;

    /**
     * Send, or replace, the proof of a commitment: a public address where the work can be seen.
     *
     * The core tickets its support inbox, and the rate moves only once the
     * owner verifies the work; a verified commitment takes no proof.
     *
     * @param   CreatorRef               $creator       The creator talking.
     * @param   string                   $commitmentId  The commitment.
     * @param   string                   $url           The public address.
     * @return  PartnerCommitmentRecord  The commitment as it now stands.
     *
     * @throws  PartnerRefused  When the ref names no creator, they were never approved, the id names none of their commitments, the address is no URL, or the commitment is verified already.
     */
    public function submitProof(CreatorRef $creator, string $commitmentId, string $url): PartnerCommitmentRecord;

    /**
     * What an add-a-commitment conversation asks before its first question.
     *
     * @param   CreatorRef                $creator  The creator talking.
     * @return  PartnerCommitmentOptions  The kinds the partner may still add with their channels, the audience bands that earn, the floor and the proof window.
     *
     * @throws  PartnerRefused  When the ref names no creator, or they were never approved.
     */
    public function commitmentOptions(CreatorRef $creator): PartnerCommitmentOptions;

    /**
     * The audiences the application declared that a kind can be done on above the reach floor.
     *
     * @param   CreatorRef                   $creator  The creator talking.
     * @param   string                       $kind     The kind's value.
     * @return  list<PartnerAudienceOption>  The audiences, keyed by their position on the application; empty when none fits.
     *
     * @throws  PartnerRefused  When the ref names no creator, they were never approved, or the kind is unknown.
     */
    public function audiencesFor(CreatorRef $creator, string $kind): array;

    /**
     * Add a commitment the application did not make, its proof due in the configured window from today.
     *
     * @param   CreatorRef               $creator  The creator talking.
     * @param   PartnerCommitmentDraft   $draft    The kind and the audience, declared or new.
     * @return  PartnerCommitmentRecord  The promise as written.
     *
     * @throws  PartnerRefused  When the ref names no creator, they were never approved, the draft is unreadable, the kind is held already, or the audience does not fit the kind or sits below the reach floor.
     */
    public function addCommitment(CreatorRef $creator, PartnerCommitmentDraft $draft): PartnerCommitmentRecord;

    /**
     * What a payout-details conversation asks, before its first question.
     *
     * @param   CreatorRef            $creator  The creator talking.
     * @return  PartnerPayoutOptions  The rails and what each needs, the minimum, the settlement sentence and whether the terms are accepted.
     *
     * @throws  PartnerRefused  When the ref names no creator, or they were never approved.
     */
    public function payoutOptions(CreatorRef $creator): PartnerPayoutOptions;

    /**
     * Record how the partner wants to be paid, accepting the terms on the way when the draft says so.
     *
     * @param   CreatorRef          $creator  The creator talking.
     * @param   PartnerPayoutDraft  $draft    The rail and what it needs.
     * @return  PartnerSummary      The standing as it now reads.
     *
     * @throws  PartnerRefused  When the ref names no creator, they were never approved, the terms are not accepted, the rail is off or unknown, or what the rail needs is missing or incomplete.
     */
    public function updatePayoutDetails(CreatorRef $creator, PartnerPayoutDraft $draft): PartnerSummary;

    /**
     * Accept the Partner Program Terms, the version in force today.
     *
     * @param   CreatorRef      $creator  The creator talking.
     * @return  PartnerSummary  The standing as it now reads.
     *
     * @throws  PartnerRefused  When the ref names no creator, or they were never approved.
     */
    public function acceptTerms(CreatorRef $creator): PartnerSummary;
}
