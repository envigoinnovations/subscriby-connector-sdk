<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;

/**
 * The touch the core recorded when a member arrived through an affiliate.
 *
 * Enough for a connector to tell the member what happened and what, if
 * anything, the programme promises them; the attribution and the rewards
 * that follow their first payment stay the core's.
 */
final readonly class ReferralCapture
{
    /**
     * @param  string                  $referralId       The touch row's UUID.
     * @param  string                  $affiliateId      The affiliate the member is now attributed to.
     * @param  DateTimeImmutable|null  $expiresAt        When the touch lapses without a first payment, or null when it never does.
     * @param  string|null             $welcomeSentence  What the member is told about their own reward, or null when the programme offers friends nothing.
     */
    public function __construct(
        public string $referralId,
        public string $affiliateId,
        public ?DateTimeImmutable $expiresAt,
        public ?string $welcomeSentence,
    ) {}
}
