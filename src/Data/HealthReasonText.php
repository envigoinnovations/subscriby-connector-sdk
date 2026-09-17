<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A connector's own words for one of the core's health reason codes.
 *
 * The core records why an installation or a place stopped answering as a
 * stable machine code (`connector_api_unauthorized`, `chat_not_found`) that
 * its webhooks carry; the sentence a creator reads for that code is the
 * platform's business ("the token was revoked in @BotFather"), so the
 * connector supplies the label and the remedy and the core keeps only a
 * generic fallback.
 */
final readonly class HealthReasonText
{
    /**
     * @param  string  $label        The short label a badge or a subject line shows.
     * @param  string  $explanation  The sentence with the most likely cause and the remedy, Markdown allowed.
     */
    public function __construct(
        public string $label,
        public string $explanation,
    ) {}
}
