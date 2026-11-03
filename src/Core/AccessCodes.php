<?php

declare(strict_types=1);

namespace Subscriby\Connector\Core;

use Subscriby\Connector\Data\AccessCodeBatch;
use Subscriby\Connector\Data\AccessCodeCost;
use Subscriby\Connector\Data\AccessCodeReceipt;
use Subscriby\Connector\Data\AccessCodeSummary;
use Subscriby\Connector\Data\CreatorRef;
use Subscriby\Connector\Enums\AccessCodeState;
use Subscriby\Connector\Exceptions\AccessCodesRefused;

/**
 * A plan's access codes, as a connector's creator surface prices, generates, lists and revokes them.
 *
 * An access code is one code for one person that grants the plan outright,
 * the way `Storefront::redeemAccessCode()` completes it on a member's
 * surface. A batch is generated on the queue and delivered to the creator
 * as a file or one message per code, through the connector their account
 * is on, so the call answers a receipt rather than the codes. Every call
 * takes the `CreatorRef` of the account talking and runs as that creator,
 * and every refusal reaches the connector as `AccessCodesRefused` with a
 * stable reason.
 */
interface AccessCodes
{
    /**
     * What a batch would cost before it is queued: the free codes left on the creator's plan and the overage.
     *
     * @param   CreatorRef      $creator   The creator talking.
     * @param   string          $planId    The plan the codes would attach to.
     * @param   int             $quantity  How many codes.
     * @return  AccessCodeCost  The free remainder, the chargeable quantity and the cost in words.
     *
     * @throws  AccessCodesRefused  When the creator or the plan is unknown, or the quantity is outside the batch's bounds.
     */
    public function previewCost(CreatorRef $creator, string $planId, int $quantity): AccessCodeCost;

    /**
     * Queue a batch; the codes reach the creator on their account once generated.
     *
     * @param   CreatorRef         $creator  The creator talking.
     * @param   AccessCodeBatch    $batch    The plan, the quantity, how the codes are delivered and when they expire.
     * @return  AccessCodeReceipt  The batch's key and the cost the creator agreed to.
     *
     * @throws  AccessCodesRefused  When the creator or the plan is unknown, a field is refused, or a batch for the plan is still in flight.
     */
    public function generate(CreatorRef $creator, AccessCodeBatch $batch): AccessCodeReceipt;

    /**
     * @param   CreatorRef               $creator  The creator talking.
     * @param   string                   $planId   The plan.
     * @param   AccessCodeState          $state    Which codes: every one, the redeemable, the used or the expired.
     * @param   int                      $limit    The most codes to answer, newest first.
     * @return  list<AccessCodeSummary>  The codes, masked so a secret never leaves the core after generation.
     *
     * @throws  AccessCodesRefused  When the creator or the plan is unknown.
     */
    public function list(CreatorRef $creator, string $planId, AccessCodeState $state = AccessCodeState::All, int $limit = 50): array;

    /**
     * Revoke an unredeemed code, so it can no longer be redeemed.
     *
     * @param  CreatorRef  $creator  The creator talking.
     * @param  string      $planId   The plan the code belongs to.
     * @param  string      $codeId   The code's id, from `list()`.
     *
     * @throws  AccessCodesRefused  When the creator, the plan or the code is unknown, or the code was already redeemed.
     */
    public function revoke(CreatorRef $creator, string $planId, string $codeId): void;
}
