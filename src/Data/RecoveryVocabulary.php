<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The words the core's recovery pages use to talk about one connector's world.
 *
 * The incident ledger, the readiness list, the banner and the recovery wizard
 * are core, but "your bot was banned from the channel" and "your app lost the
 * server" are the same sentence with different nouns; the connector supplies
 * them so the core never writes a platform's word. The three notes and the
 * step list carry the facts only the platform knows (what is mirrored and
 * why, what a member has to do after an installation is replaced, what the
 * creator does after their account moved); the core shows them where they
 * belong and leaves the space empty for a connector that has nothing to add.
 */
final readonly class RecoveryVocabulary
{
    /**
     * @param  string        $installationNoun               What an installation is called ("bot", "app").
     * @param  string        $spaceNoun                      What a place is called ("channel", "server").
     * @param  string        $identityNoun                   What an account is called ("Telegram account", "Discord account").
     * @param  string        $grantNoun                      What a grant is called ("invite link", "role").
     * @param  string        $installationsNoun              The installation noun in the plural ("bots", "apps").
     * @param  string        $spacesNoun                     The place noun in the plural ("channels and groups", "servers").
     * @param  string|null   $mirrorNote                     Which places a standby mirrors and why, when the connector mirrors at all.
     * @param  string|null   $installationHandoverNote       What members have to do after an installation was replaced, when the platform asks anything of them.
     * @param  string|null   $installationMovedAnnouncement  The message a creator pastes to their members after an installation was replaced, with `:project` and `:url` placeholders.
     * @param  list<string>  $identityRelinkedSteps          What the creator does on the platform after their account moved, one sentence per step.
     */
    public function __construct(
        public string $installationNoun,
        public string $spaceNoun,
        public string $identityNoun,
        public string $grantNoun,
        public string $installationsNoun,
        public string $spacesNoun,
        public ?string $mirrorNote = null,
        public ?string $installationHandoverNote = null,
        public ?string $installationMovedAnnouncement = null,
        public array $identityRelinkedSteps = [],
    ) {}

    /**
     * The announcement with the project and the link filled in.
     *
     * @param   string       $project  The project's name.
     * @param   string       $url      Where members open the new installation.
     * @return  string|null  The text to paste, or null when the connector has none.
     */
    public function announcementFor(string $project, string $url): ?string
    {
        if ($this->installationMovedAnnouncement === null) {
            return null;
        }

        return strtr($this->installationMovedAnnouncement, [':project' => $project, ':url' => $url]);
    }
}
