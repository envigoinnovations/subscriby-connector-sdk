<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One prose section of a connector's search landing page.
 *
 * A section is a heading and a body of one or more paragraphs separated by
 * blank lines, so a connector can explain how a paid channel works on its
 * platform, how members pay there, or what happens when the platform bans
 * a bot, in its own words and with the platform named.
 */
final readonly class ListingSeoSection
{
    /**
     * @param  string  $heading  The section's heading, translated through the package's language files.
     * @param  string  $body     One or more paragraphs separated by blank lines.
     */
    public function __construct(
        public string $heading,
        public string $body,
    ) {}
}
