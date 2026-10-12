<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\CancellationRecord;
use Subscriby\Connector\Data\CancellationScreen;
use Subscriby\Connector\Data\IdentityRecord;
use Subscriby\Connector\Data\InstallationRef;
use Subscriby\Connector\Data\PaymentProviderKey;
use Subscriby\Connector\Data\RetentionOfferOutcome;
use Subscriby\Connector\Data\SequenceArrival;
use Subscriby\Connector\Exceptions\RetentionRefused;

/**
 * The cancellation screen of Revenue Recovery, as a connector's member surface shows it.
 *
 * A member who asks to cancel is first asked why, when the project wants to
 * know, then shown the one offer the project makes on their rail (a pause
 * for one billing cycle, a discount on the renewals, or a coupon to come
 * back with), and only then the connector's own confirmation. The reasons,
 * the offer and every sentence are the core's, so the bot and the portal
 * ask and offer the same things in the same words, and a connector decides
 * nothing: it draws what it is handed and reports what was tapped. Every
 * call names the project through the installation the conversation runs on
 * and the member through the account that is talking.
 */
interface Retention
{
    /**
     * What to show a member who asked to cancel their live membership.
     *
     * @param   InstallationRef          $installation  The project's installation the conversation runs on.
     * @param   IdentityRecord           $member        The account that is talking.
     * @return  CancellationScreen|null  The question and the offer, or null when the account holds no live membership to cancel.
     *
     * @throws  RetentionRefused  `installation_unknown` when the ref names no project.
     */
    public function cancellationScreenFor(InstallationRef $installation, IdentityRecord $member): ?CancellationScreen;

    /**
     * Write down why the member is leaving, tell the creator, and answer the offer to show next.
     *
     * @param   InstallationRef     $installation  The project's installation the conversation runs on.
     * @param   IdentityRecord      $member        The account that is talking.
     * @param   string              $reason        The reason's code, as the screen listed it.
     * @param   string|null         $note          The sentence they added, or null.
     * @return  CancellationRecord  The row, with the offer to show next.
     *
     * @throws  RetentionRefused  `installation_unknown`, `member_unknown`, `subscription_unknown`, or `reason_unknown` for a code the list does not carry.
     */
    public function recordCancellationFeedback(InstallationRef $installation, IdentityRecord $member, string $reason, ?string $note): CancellationRecord;

    /**
     * Give the member the offer the screen made, instead of the cancellation.
     *
     * The offer is derived again by the core, never taken from the
     * connector, so a stale screen cannot claim one the creator has since
     * withdrawn. A pause holds the charge at the gateway first and pauses
     * access second; a discount is applied at the gateway; a return coupon
     * is handed over, on the screen and in a message, and the cancellation
     * goes on.
     *
     * @param   InstallationRef        $installation    The project's installation the conversation runs on.
     * @param   IdentityRecord         $member          The account that is talking.
     * @param   string|null            $cancellationId  The row their reason was written to, or null when the project asked none.
     * @return  RetentionOfferOutcome  What they got, with the sentence to print.
     *
     * @throws  RetentionRefused  `installation_unknown`, `member_unknown`, `subscription_unknown`, `cancellation_unknown`, `offer_unavailable`, or `gateway_refused` when the gateway would not hold the charge or apply the discount.
     */
    public function takeOffer(InstallationRef $installation, IdentityRecord $member, ?string $cancellationId): RetentionOfferOutcome;

    /**
     * Note that the member passed on the offer, so the offers shown are counted.
     *
     * @param   InstallationRef          $installation    The project's installation the conversation runs on.
     * @param   IdentityRecord           $member          The account that is talking.
     * @param   string|null              $cancellationId  The row their reason was written to, or null when the project asked none.
     * @return  CancellationRecord|null  The row that carries the offer shown, or null when the project makes none on their rail.
     *
     * @throws  RetentionRefused  `installation_unknown`, `member_unknown`, `subscription_unknown` or `cancellation_unknown`.
     */
    public function declineOffer(InstallationRef $installation, IdentityRecord $member, ?string $cancellationId): ?CancellationRecord;

    /**
     * Write a checkout the connector's own door just opened to the checkout ledger.
     *
     * A bot hands the member the gateway's page or invoice itself, so the
     * core never saw the start; this is how the ledger learns of it, so
     * the hourly sweep can count the checkout as stalled once the project's
     * window has passed and the member can be reminded. The core raises
     * `payment.pending` for it, as the portal's doors do for theirs.
     *
     * @param   InstallationRef     $installation  The project's installation the conversation runs on.
     * @param   IdentityRecord      $member        The account that is buying.
     * @param   string              $planId        The plan they are buying.
     * @param   PaymentProviderKey  $provider      The method row's key: a gateway's word, or a connector's native provider.
     * @param   string|null         $methodId      The gateway row the checkout opened on, or null for a rail with none.
     * @return  bool                True when the row was written; false when the project or the member is unknown or the rail is not one the core keys.
     *
     * @throws  RetentionRefused  `installation_unknown` when the ref names no project.
     */
    public function recordCheckoutStart(InstallationRef $installation, IdentityRecord $member, string $planId, PaymentProviderKey $provider, ?string $methodId): bool;

    /**
     * Receive a member who opened the project's bot through a sequence step's button.
     *
     * The deep link a step carries is `seq_` and the passage's id. The core
     * checks the passage is the member's own in this project, records the
     * step's coupon for them on the plan the passage is about, and answers
     * the plan to open; a passage that is not theirs, or that the core does
     * not know, answers null and the connector shows its home as for a
     * plain start, never telling the member a link was involved.
     *
     * @param   InstallationRef       $installation  The project's installation the conversation runs on.
     * @param   IdentityRecord        $member        The account that arrived.
     * @param   string                $enrollmentId  The passage id after `seq_`.
     * @return  SequenceArrival|null  The plan to open with the coupon recorded, or null when the link was not the member's.
     *
     * @throws  RetentionRefused  `installation_unknown` when the ref names no project.
     */
    public function arriveFromSequence(InstallationRef $installation, IdentityRecord $member, string $enrollmentId): ?SequenceArrival;

    /**
     * Stop every sequence for a member who pressed the Stop button a step carries.
     *
     * Every open passage of theirs in the project ends, and no sequence
     * enrols them again until a charge settles for them or they switch the
     * follow-ups back on from the portal.
     *
     * @param   InstallationRef  $installation  The project's installation the conversation runs on.
     * @param   IdentityRecord   $member        The account that asked.
     * @return  bool             True when the member was found and stamped; false for an account the project does not know.
     *
     * @throws  RetentionRefused  `installation_unknown` when the ref names no project.
     */
    public function stopSequences(InstallationRef $installation, IdentityRecord $member): bool;
}
