<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One line of the disaster-recovery readiness checklist, as a connector contributes it.
 *
 * The core keeps the checks it can verify itself; a connector adds the ones
 * that live on its platform (a standby bot, a mirrored channel), in its own
 * words, and the checklist groups them under the connector's name.
 */
final readonly class ReadinessItem
{
    /**
     * @param  string       $key          A stable identifier within the connector.
     * @param  string       $label        What the item is called.
     * @param  string       $description  Why it matters, in one sentence.
     * @param  string       $icon         The Heroicon name the tile draws.
     * @param  bool         $satisfied    Whether the creator has done it.
     * @param  bool         $prevention   Whether it is a paid prevention feature, shown locked without the capability.
     * @param  string|null  $fixRoute     Where the creator goes to do it, when there is a page.
     */
    public function __construct(
        public string $key,
        public string $label,
        public string $description,
        public string $icon,
        public bool $satisfied,
        public bool $prevention = false,
        public ?string $fixRoute = null,
    ) {}
}
