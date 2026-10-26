<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One question and its answer in the portal's FAQ block, as a creator reads it on a connector.
 */
final readonly class PortalFaqItemDetails
{
    /**
     * @param  string  $question  The question, one line.
     * @param  string  $answer    The answer, a short text that may run over a few lines.
     */
    public function __construct(
        public string $question,
        public string $answer,
    ) {}
}
