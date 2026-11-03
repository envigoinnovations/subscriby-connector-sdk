<?php

declare(strict_types=1);

namespace Subscriby\Connector\Data;

/**
 * One of a creator's projects, as a list on a creator surface names it.
 *
 * Enough to pick one and to say whether it sells anything yet; the
 * `ProjectDetails` behind `Core\Projects::find()` carry the rest.
 */
final readonly class ProjectSummary
{
    /**
     * @param  string       $id         The project's UUID.
     * @param  string       $name       The project's name.
     * @param  string|null  $handle     The portal handle, null while the project has none.
     * @param  bool         $active     Whether the project is live rather than archived or a draft.
     * @param  string|null  $connector  The connector of its live installation, null while nothing is connected.
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $handle,
        public bool $active,
        public ?string $connector = null,
    ) {}

    /**
     * @return  ProjectRef  The ref every other contract takes.
     */
    public function ref(): ProjectRef
    {
        return new ProjectRef($this->id);
    }
}
