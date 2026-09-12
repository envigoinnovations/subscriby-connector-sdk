<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * A project's recovery standing on one connector, the facts a readiness item is worded from.
 */
final readonly class RecoveryCoverage
{
    /**
     * @param  bool                 $standbyInstallation  Whether a standby installation is registered for the project on this connector.
     * @param  bool                 $autoFailover         Whether the project switches to a standby space on its own.
     * @param  list<SpaceCoverage>  $spaces               One entry per resource the connector gates for the project.
     */
    public function __construct(
        public bool $standbyInstallation,
        public bool $autoFailover,
        public array $spaces = [],
    ) {}

    /**
     * @return  list<SpaceCoverage>  The spaces that already have a standby.
     */
    public function coveredSpaces(): array
    {
        return array_values(array_filter($this->spaces, static fn (SpaceCoverage $space): bool => $space->hasStandby));
    }

    /**
     * @param   string               $kind  A resource kind within the connector.
     * @return  list<SpaceCoverage>  The spaces of that kind.
     */
    public function spacesOfKind(string $kind): array
    {
        return array_values(array_filter($this->spaces, static fn (SpaceCoverage $space): bool => $space->kind === $kind));
    }
}
