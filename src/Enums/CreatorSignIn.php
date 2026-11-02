<?php

declare(strict_types=1);

namespace Subscriby\Connector\Enums;

use Subscriby\Connector\Enums\Concerns\EnumHelpers;

/**
 * What came of a connector's attempt to sign a creator in by an account the platform vouched for.
 *
 * The core runs the same login the password form runs, so a creator who set
 * up a second factor is parked for the challenge rather than signed in; an
 * account no creator holds is the connector's to send to the sign-up page.
 */
enum CreatorSignIn: string
{
    use EnumHelpers;

    case SignedIn = 'signed_in';
    case ChallengeRequired = 'challenge_required';
    case Unknown = 'unknown';

    /**
     * @return  bool  True when the creator now has a session.
     */
    public function isSignedIn(): bool
    {
        return $this === self::SignedIn;
    }
}
