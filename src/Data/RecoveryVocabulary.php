<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The nouns the core's recovery pages use to talk about one connector's world.
 *
 * The incident ledger, the readiness list and the banner are core, but "your
 * bot was banned from the channel" and "your app lost the server" are the same
 * sentence with different nouns; the connector supplies them so the core never
 * writes a Telegram word.
 */
final readonly class RecoveryVocabulary
{
    /**
     * @param  string  $installationNoun  What an installation is called ("bot", "app").
     * @param  string  $spaceNoun         What a place is called ("channel", "server").
     * @param  string  $identityNoun      What an account is called ("Telegram account", "Discord account").
     * @param  string  $grantNoun         What a grant is called ("invite link", "role").
     */
    public function __construct(
        public string $installationNoun,
        public string $spaceNoun,
        public string $identityNoun,
        public string $grantNoun,
    ) {}
}
