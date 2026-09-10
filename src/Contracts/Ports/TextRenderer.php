<?php

declare(strict_types=1);

namespace Subscriby\Connector\Contracts\Ports;

/**
 * Turning the core's canonical HTML into what one platform accepts.
 *
 * Required of every connector, because everything the core says to a person
 * is written once in the canonical subset (`<b> <i> <u> <s> <a> <code> <pre>
 * <blockquote>`) and rendered down here: Telegram keeps it, Discord becomes
 * Markdown, a platform with no formatting strips it.
 */
interface TextRenderer
{
    /**
     * @param   string  $canonicalHtml  The core's message body.
     * @return  string  The platform's formatting of it.
     */
    public function render(string $canonicalHtml): string;
}
