<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

use DateTimeImmutable;
use Subscriby\Connector\Enums\ListingCategory;
use Subscriby\Connector\Exceptions\InvalidManifest;

/**
 * What the directory shows about a connector, on the marketing site and in the app.
 *
 * Status, the official badge and the New/Trending chips are deliberately not
 * here: the registry stamps them from configuration and install counts, so a
 * package cannot call itself official or trending.
 */
final readonly class Listing
{
    /** The longest tagline a card can show on one line. */
    public const TAGLINE_LENGTH = 80;

    /**
     * @param  ListingCategory        $category        The shelf the connector sits on.
     * @param  string                 $tagline         One line under the name, at most 80 characters.
     * @param  string                 $overview        The Overview tab, Markdown.
     * @param  list<string>           $screenshots     Screenshot URLs.
     * @param  ListingLinks           $links           Documentation, support, privacy, terms, homepage.
     * @param  DateTimeImmutable      $addedAt         When the connector was first published; drives the New chip.
     * @param  string|null            $changelogUrl    Where releases are announced.
     * @param  bool                   $signInRequired  Whether the creator has to sign in to the platform to install.
     * @param  ListingMarketing|null  $marketing       The words the marketing site borrows; null when the connector lends none.
     *
     * @throws  InvalidManifest  When the tagline is empty or too long.
     */
    public function __construct(
        public ListingCategory $category,
        public string $tagline,
        public string $overview,
        public array $screenshots,
        public ListingLinks $links,
        public DateTimeImmutable $addedAt,
        public ?string $changelogUrl = null,
        public bool $signInRequired = true,
        public ?ListingMarketing $marketing = null,
    ) {
        $length = mb_strlen(trim($tagline));

        if ($length === 0 || $length > self::TAGLINE_LENGTH) {
            throw InvalidManifest::because('unknown', sprintf('the listing tagline must be 1 to %d characters', self::TAGLINE_LENGTH));
        }
    }
}
