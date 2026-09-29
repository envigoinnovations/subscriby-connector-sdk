<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One question and its answer on a connector's search landing page.
 *
 * The page renders them as cards and describes them to search engines as a
 * FAQPage, so each answer should stand on its own in a sentence or three.
 */
final readonly class ListingSeoQuestion
{
    /**
     * @param  string  $question  The question as a visitor would type it, translated through the package's language files.
     * @param  string  $answer    The answer, translated the same way.
     */
    public function __construct(
        public string $question,
        public string $answer,
    ) {}
}
