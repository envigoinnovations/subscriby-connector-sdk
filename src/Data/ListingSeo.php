<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * The copy that turns a connector's public page into a search landing page.
 *
 * The marketing site never spells a platform in its own copy, so the one
 * page that may lead with "Telegram membership bot" is the connector's, and
 * the words have to come from the connector. A manifest that carries this
 * block replaces the page's title, description and headline with its own,
 * adds sections of prose under the overview and a list of questions with
 * their answers, all rendered as ordinary text with the site's tokens
 * (`:app`, `:gateways`, `:webhook_events`, `:api_resources`, `:mcp_tools`)
 * filled at render time. The manifest loader holds the English title and
 * description to the lengths a search snippet shows whole; the object itself
 * carries any text, because the core rebuilds it with each string translated
 * and a translation may run longer. A connector without the block keeps the
 * generic page built from the rest of its listing.
 */
final readonly class ListingSeo
{
    /** The longest title a search result shows whole. */
    public const TITLE_LENGTH = 70;

    /** The longest description a search result shows whole. */
    public const DESCRIPTION_LENGTH = 160;

    /**
     * @param  string                    $title        The page title, at most 70 characters, a search phrase first.
     * @param  string                    $description  The meta description, at most 160 characters.
     * @param  string                    $h1           The page's one headline.
     * @param  string|null               $h1Sub        The line under the headline; the tagline when null.
     * @param  list<ListingSeoSection>   $sections     Prose sections under the overview, in order.
     * @param  list<ListingSeoQuestion>  $faq          Questions and their answers, in order.
     */
    public function __construct(
        public string $title,
        public string $description,
        public string $h1,
        public ?string $h1Sub = null,
        public array $sections = [],
        public array $faq = [],
    ) {}
}
