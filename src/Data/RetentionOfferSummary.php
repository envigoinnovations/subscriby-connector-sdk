<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use Subscriby\Connector\Enums\RetentionOfferKind;

/**
 * The one offer a cancelling member is shown, in the core's words.
 *
 * The headline, the sentence and the button label are phrased once by the
 * core in the member's language, so the bot and the portal make the same
 * offer the same way; a connector prints them and adds nothing. A return
 * coupon carries its code, because the member keeps it.
 */
final readonly class RetentionOfferSummary
{
    /**
     * @param  RetentionOfferKind  $kind         Which offer.
     * @param  string              $headline     The question the screen asks ("Pause instead?").
     * @param  string              $sentence     What the offer gives, in one sentence.
     * @param  string              $acceptLabel  What the button that takes the offer says.
     * @param  string|null         $couponCode   The coupon's code for a discount or a return coupon; null for a pause.
     */
    public function __construct(
        public RetentionOfferKind $kind,
        public string $headline,
        public string $sentence,
        public string $acceptLabel,
        public ?string $couponCode,
    ) {}
}
