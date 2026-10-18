<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * Everything a turn has done so far, carried whole on every repaint.
 *
 * The thinking is the model's own reasoning as far as it has come, the tools
 * are named in words as the core labels them, and the answer travels twice:
 * as the Markdown the model writes, for a platform that renders it, and as
 * the canonical HTML subset every connector's renderer takes.
 */
final readonly class CopilotTurnProgress
{
    /**
     * @param  string        $thinking  The reasoning so far, empty until the model shows any.
     * @param  list<string>  $tools     The tools reached for so far, in order, each in words.
     * @param  string        $markdown  The answer so far as Markdown, with nothing the creator should not see.
     * @param  string        $html      The same answer as canonical HTML.
     */
    public function __construct(
        public string $thinking,
        public array $tools,
        public string $markdown,
        public string $html,
    ) {}
}
