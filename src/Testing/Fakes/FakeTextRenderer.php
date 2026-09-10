<?php

declare(strict_types=1);

namespace Subscriby\Connector\Testing\Fakes;

use Subscriby\Connector\Contracts\Ports\TextRenderer;

/**
 * Renders the canonical HTML to plain text, as a platform with no formatting would.
 */
final class FakeTextRenderer implements TextRenderer
{
    /**
     * @param   string  $canonicalHtml  The core's body.
     * @return  string  Tags stripped, entities decoded.
     */
    public function render(string $canonicalHtml): string
    {
        return html_entity_decode(strip_tags($canonicalHtml), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
