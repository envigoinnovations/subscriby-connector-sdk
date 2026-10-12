<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\RetentionOfferKind;

/**
 * What came of a member taking the cancellation screen's offer.
 *
 * The sentence is the core's: what the member now has, in their language,
 * ready to print. A pause or a discount keeps the membership, so the
 * connector ends there; a return coupon does not, so the connector goes on
 * to its confirmation with the code in hand.
 */
final readonly class RetentionOfferOutcome
{
    /**
     * @param  RetentionOfferKind  $kind               The offer taken.
     * @param  bool                $keepsSubscription  True when the membership stays, so no cancellation follows.
     * @param  string              $sentence           What the member now has, in one sentence.
     * @param  string|null         $couponCode         The code handed over or applied, when the offer carried one.
     */
    public function __construct(
        public RetentionOfferKind $kind,
        public bool $keepsSubscription,
        public string $sentence,
        public ?string $couponCode,
    ) {}
}
