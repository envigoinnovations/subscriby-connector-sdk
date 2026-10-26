<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One question and its answer in the portal's FAQ block, as a creator sends it.
 */
final readonly class PortalFaqItemDraft
{
    /**
     * @param  string  $question  The question, one line, within `PortalDesignDetails::$blockLimits['faq_question']`.
     * @param  string  $answer    The answer, within `PortalDesignDetails::$blockLimits['faq_answer']`.
     */
    public function __construct(
        public string $question,
        public string $answer,
    ) {}
}
