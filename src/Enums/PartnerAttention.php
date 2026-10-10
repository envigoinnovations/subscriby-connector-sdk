<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * The one thing an approved partner has to see before anything else on their screen.
 *
 * The dashboard shows the same callout: a suspended partnership comes
 * first, then the terms not yet accepted, then the payout details not yet
 * given; an approved partner with none of these has nothing in the way of
 * being paid.
 */
enum PartnerAttention: string
{
    use EnumHelpers;

    case Suspended = 'suspended';
    case TermsNotAccepted = 'terms_not_accepted';
    case PayoutDetailsMissing = 'payout_details_missing';
}
